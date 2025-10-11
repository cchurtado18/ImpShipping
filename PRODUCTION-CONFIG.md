# 🚀 GUÍA DE CONFIGURACIÓN PARA PRODUCCIÓN

## 📋 CONFIGURACIÓN DEL ARCHIVO .ENV EN PRODUCCIÓN

Copia este contenido a tu archivo `.env` en el servidor y ajusta los valores según tu configuración:

```env
APP_NAME="IMPEF Shipping"
APP_ENV=production
APP_KEY=base64:TU_APP_KEY_AQUI
APP_DEBUG=false
APP_TIMEZONE=America/Managua
APP_URL=https://tu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=impef_shipping
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_password_db

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

QUEUE_CONNECTION=database

CACHE_STORE=file

LOG_CHANNEL=stack
LOG_LEVEL=error

FILESYSTEM_DISK=local
```

## ⚠️ CONFIGURACIONES CRÍTICAS

### 1. **APP_DEBUG**
```env
APP_DEBUG=false  # SIEMPRE false en producción
```
**Si está en `true`, mostrará errores técnicos a los usuarios.**

### 2. **APP_ENV**
```env
APP_ENV=production  # Debe ser 'production'
```

### 3. **SESSION_DRIVER**
```env
SESSION_DRIVER=database  # Más estable que 'file' en producción
```

Si usas `database` para sesiones, asegúrate de tener la tabla `sessions`:
```bash
php artisan session:table
php artisan migrate
```

### 4. **APP_KEY**
Si no tienes un `APP_KEY`, genera uno:
```bash
php artisan key:generate
```

## 🔧 COMANDOS PARA EJECUTAR EN EL SERVIDOR

### Opción 1: Script Automático (RECOMENDADO)
```bash
# Hacer el script ejecutable
chmod +x deploy-production.sh

# Ejecutar el script
./deploy-production.sh
```

### Opción 2: Manual (paso a paso)

#### 1. Actualizar código
```bash
git pull origin main
```

#### 2. Instalar dependencias
```bash
composer install --no-dev --optimize-autoloader
```

#### 3. Configurar permisos
```bash
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

#### 4. Limpiar cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### 5. Storage link
```bash
php artisan storage:link
```

#### 6. Migraciones
```bash
php artisan migrate --force
```

#### 7. Sincronizar facturas
```bash
php artisan sync:invoice-status
```

#### 8. Optimizar
```bash
php artisan config:cache
php artisan route:cache
# NO ejecutar view:cache si da problemas
```

#### 9. Reiniciar servicios
```bash
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

## 🐛 PROBLEMAS COMUNES Y SOLUCIONES

### Problema 1: No se guardan las facturas
**Causa:** Permisos incorrectos en `storage/` o sesiones no configuradas.

**Solución:**
```bash
sudo chmod -R 775 storage
sudo chown -R www-data:www-data storage
```

Verifica que `.env` tenga:
```env
SESSION_DRIVER=database
```

Y ejecuta:
```bash
php artisan session:table
php artisan migrate
```

### Problema 2: Assets (CSS/JS) no cargan
**Causa:** Assets no compilados o permisos incorrectos.

**Solución:**
```bash
# Verifica que existan
ls -la public/build/

# Ajusta permisos
sudo chmod -R 755 public/build/
```

### Problema 3: Error 500 al guardar
**Causa:** Problemas con logs o cache.

**Solución:**
```bash
# Limpiar logs
sudo truncate -s 0 storage/logs/laravel.log

# Limpiar todo el cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Problema 4: "auth-session-status" component not found
**Causa:** Problema con cache de vistas.

**Solución:**
```bash
php artisan view:clear
# NO ejecutar view:cache
```

## 🔍 VERIFICACIÓN POST-DESPLIEGUE

### 1. Verificar conexión a base de datos
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
```

### 2. Verificar permisos
```bash
ls -la storage/
ls -la bootstrap/cache/
```

### 3. Probar creación de factura
- Accede a la aplicación
- Ve a "Invoices"
- Crea una factura de prueba
- Verifica que se guarde correctamente

### 4. Revisar logs si hay errores
```bash
tail -f storage/logs/laravel.log
```

## 📊 MONITOREO

### Ver logs en tiempo real
```bash
tail -f storage/logs/laravel.log
```

### Ver últimos errores
```bash
tail -50 storage/logs/laravel.log | grep ERROR
```

### Limpiar logs antiguos
```bash
sudo truncate -s 0 storage/logs/laravel.log
```

## 🔄 ACTUALIZACIONES FUTURAS

Cada vez que hagas cambios y quieras desplegarlos:

```bash
./deploy-production.sh
```

O manualmente:
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
sudo systemctl restart php8.1-fpm nginx
```

## ⚡ OPTIMIZACIONES OPCIONALES

### 1. OPcache (PHP)
Edita `/etc/php/8.1/fpm/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
```

### 2. Queue Workers (para mejor rendimiento)
```bash
php artisan queue:work --daemon
```

### 3. Supervisor (para mantener queue workers)
```bash
sudo apt install supervisor
```

## 📞 SOPORTE

Si después de seguir esta guía aún tienes problemas:

1. Revisa los logs: `tail -f storage/logs/laravel.log`
2. Verifica permisos: `ls -la storage/`
3. Verifica `.env`: `cat .env | grep APP_DEBUG`
4. Prueba en modo debug temporal: `APP_DEBUG=true` (solo para diagnosticar)

