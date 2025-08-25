# Guía de Despliegue en DigitalOcean

## 1. Crear Droplet en DigitalOcean

### Opciones Recomendadas:
- **Ubuntu 22.04 LTS** (más estable)
- **Ubuntu 24.04 LTS** (más reciente)
- **Ubuntu 20.04 LTS** (muy estable)

### Configuración del Droplet:
- **Plan**: Basic
- **CPU**: 1 vCPU
- **RAM**: 1GB (mínimo recomendado)
- **SSD**: 25GB
- **Transferencia**: 1TB
- **Ubicación**: Cerca de tus usuarios (ej: NYC3 para EE.UU.)

### Autenticación:
- **SSH Key** (recomendado) o Password
- Si usas SSH Key, agrega tu clave pública

## 2. Conectar al Servidor

```bash
# Conectar via SSH
ssh root@tu-ip-del-droplet

# O si usas clave SSH
ssh -i ~/.ssh/tu-clave root@tu-ip-del-droplet
```

## 3. Configurar el Servidor

### Actualizar el sistema:
```bash
apt update && apt upgrade -y
```

### Instalar dependencias básicas:
```bash
apt install -y curl wget git unzip software-properties-common apt-transport-https ca-certificates gnupg lsb-release
```

### Instalar Nginx:
```bash
apt install -y nginx
systemctl start nginx
systemctl enable nginx
```

### Instalar PHP 8.2:
```bash
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl php8.2-redis php8.2-sqlite3
```

### Instalar Composer:
```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### Instalar MySQL:
```bash
apt install -y mysql-server
mysql_secure_installation
```

### Instalar Node.js (para compilar assets):
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs
```

## 4. Configurar Base de Datos

```bash
mysql -u root -p
```

```sql
CREATE DATABASE impef_db;
CREATE USER 'impef_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON impef_db.* TO 'impef_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 5. Desplegar la Aplicación

### Crear directorio para la aplicación:
```bash
mkdir -p /var/www/impef
cd /var/www/impef
```

### Clonar el repositorio (si usas Git):
```bash
git clone https://github.com/tu-usuario/impef.git .
```

### O subir archivos via SCP/SFTP:
```bash
# Desde tu máquina local
scp -r /ruta/a/tu/proyecto/* root@tu-ip:/var/www/impef/
```

### Configurar permisos:
```bash
chown -R www-data:www-data /var/www/impef
chmod -R 755 /var/www/impef
chmod -R 775 /var/www/impef/storage
chmod -R 775 /var/www/impef/bootstrap/cache
```

### Instalar dependencias:
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### Configurar variables de entorno:
```bash
cp .env.example .env
nano .env
```

Configurar en `.env`:
```env
APP_NAME=IMPEF
APP_ENV=production
APP_DEBUG=false
APP_URL=http://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=impef_db
DB_USERNAME=impef_user
DB_PASSWORD=tu_password_seguro

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Generar clave de aplicación:
```bash
php artisan key:generate
```

### Ejecutar migraciones:
```bash
php artisan migrate --force
```

### Optimizar para producción:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 6. Configurar Nginx

### Crear configuración del sitio:
```bash
nano /etc/nginx/sites-available/impef
```

Contenido:
```nginx
server {
    listen 80;
    server_name tu-dominio.com www.tu-dominio.com;
    root /var/www/impef/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

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
```

### Habilitar el sitio:
```bash
ln -s /etc/nginx/sites-available/impef /etc/nginx/sites-enabled/
rm /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx
```

## 7. Configurar SSL (HTTPS)

### Instalar Certbot:
```bash
apt install -y certbot python3-certbot-nginx
```

### Obtener certificado SSL:
```bash
certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

### Renovar automáticamente:
```bash
crontab -e
# Agregar esta línea:
0 12 * * * /usr/bin/certbot renew --quiet
```

## 8. Configurar Firewall

```bash
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw enable
```

## 9. Configurar Backup Automático

### Crear script de backup:
```bash
nano /root/backup.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"
DB_NAME="impef_db"
DB_USER="impef_user"
DB_PASS="tu_password_seguro"
APP_DIR="/var/www/impef"

# Crear directorio de backup
mkdir -p $BACKUP_DIR

# Backup de la base de datos
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Backup de archivos de la aplicación
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz -C /var/www impef

# Mantener solo los últimos 7 backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completado: $DATE"
```

### Hacer ejecutable y programar:
```bash
chmod +x /root/backup.sh
crontab -e
# Agregar esta línea para backup diario a las 2 AM:
0 2 * * * /root/backup.sh
```

## 10. Monitoreo y Mantenimiento

### Instalar herramientas de monitoreo:
```bash
apt install -y htop iotop nethogs
```

### Configurar logrotate:
```bash
nano /etc/logrotate.d/impef
```

```
/var/www/impef/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
}
```

## 11. Optimizaciones Adicionales

### Configurar PHP-FPM:
```bash
nano /etc/php/8.2/fpm/php.ini
```

Ajustar valores:
```ini
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 64M
post_max_size = 64M
```

### Reiniciar servicios:
```bash
systemctl restart php8.2-fpm
systemctl restart nginx
```

## 12. Verificación Final

1. **Probar la aplicación**: http://tu-dominio.com
2. **Verificar logs**: `tail -f /var/log/nginx/error.log`
3. **Verificar estado de servicios**: `systemctl status nginx php8.2-fpm mysql`
4. **Probar SSL**: https://tu-dominio.com

## Troubleshooting

### Problemas comunes:
- **Error 502**: Verificar PHP-FPM
- **Error 500**: Verificar logs de Laravel
- **Error de permisos**: Verificar ownership de archivos
- **Error de base de datos**: Verificar conexión MySQL

### Comandos útiles:
```bash
# Ver logs de Laravel
tail -f /var/www/impef/storage/logs/laravel.log

# Ver logs de Nginx
tail -f /var/log/nginx/error.log

# Ver logs de PHP-FPM
tail -f /var/log/php8.2-fpm.log

# Reiniciar servicios
systemctl restart nginx php8.2-fpm mysql
```
