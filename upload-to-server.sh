#!/bin/bash

# Script para subir la aplicación IMPEF al servidor DigitalOcean
# Ejecutar desde tu máquina local

# Configuración
SERVER_IP="TU_IP_DEL_SERVIDOR"
SERVER_USER="root"
APP_DIR="/var/www/impef"
LOCAL_DIR="."

echo "🚀 Subiendo aplicación IMPEF al servidor..."

# Verificar que estás en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz de Laravel."
    exit 1
fi

# Crear archivo temporal sin node_modules y vendor
echo "📦 Preparando archivos para subir..."
TEMP_DIR="/tmp/impef_upload_$(date +%s)"
mkdir -p $TEMP_DIR

# Copiar archivos necesarios
rsync -av --exclude='node_modules' \
         --exclude='vendor' \
         --exclude='.git' \
         --exclude='storage/logs/*' \
         --exclude='storage/framework/cache/*' \
         --exclude='storage/framework/sessions/*' \
         --exclude='storage/framework/views/*' \
         --exclude='.env' \
         --exclude='.env.example' \
         --exclude='deployment-guide.md' \
         --exclude='deploy.sh' \
         --exclude='upload-to-server.sh' \
         $LOCAL_DIR/ $TEMP_DIR/

# Crear archivo .env de producción
cat > $TEMP_DIR/.env << EOF
APP_NAME=IMPEF
APP_ENV=production
APP_DEBUG=false
APP_URL=http://$SERVER_IP

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=impef_db
DB_USERNAME=impef_user
DB_PASSWORD=REEMPLAZAR_CON_PASSWORD_DEL_SERVIDOR

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

# Subir archivos al servidor
echo "📤 Subiendo archivos al servidor..."
rsync -avz --delete $TEMP_DIR/ $SERVER_USER@$SERVER_IP:$APP_DIR/

# Limpiar archivos temporales
rm -rf $TEMP_DIR

# Ejecutar comandos en el servidor
echo "🔧 Configurando aplicación en el servidor..."
ssh $SERVER_USER@$SERVER_IP << 'EOF'
cd /var/www/impef

# Configurar permisos
chown -R www-data:www-data /var/www/impef
chmod -R 755 /var/www/impef
chmod -R 775 /var/www/impef/storage
chmod -R 775 /var/www/impef/bootstrap/cache

# Instalar dependencias de PHP
composer install --no-dev --optimize-autoloader

# Instalar dependencias de Node.js
npm install

# Compilar assets
npm run build

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones
php artisan migrate --force

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Reiniciar servicios
systemctl restart php8.2-fpm
systemctl reload nginx

echo "✅ Aplicación configurada exitosamente!"
EOF

echo ""
echo "🎉 ¡Aplicación subida y configurada exitosamente!"
echo ""
echo "📋 Información:"
echo "   Servidor: $SERVER_IP"
echo "   URL: http://$SERVER_IP"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   1. Actualiza la contraseña de la base de datos en el archivo .env del servidor"
echo "   2. Configura tu dominio en Nginx si tienes uno"
echo "   3. Configura SSL con Certbot para HTTPS"
echo ""
echo "🔗 Para acceder a la aplicación: http://$SERVER_IP"
