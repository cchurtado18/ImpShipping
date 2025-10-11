#!/bin/bash

echo "=== SCRIPT DE REPARACIÓN DEL SERVIDOR ==="
echo "Ejecutando correcciones automáticas..."
echo ""

# 1. Ir al directorio del proyecto
echo "1. Verificando directorio del proyecto..."
if [ -f "artisan" ]; then
    echo "   ✅ En el directorio correcto"
else
    echo "   ❌ No estás en el directorio del proyecto Laravel"
    echo "   Cambia al directorio correcto y ejecuta este script nuevamente"
    exit 1
fi

# 2. Instalar/actualizar dependencias
echo ""
echo "2. Instalando dependencias..."
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -eq 0 ]; then
    echo "   ✅ Dependencias instaladas correctamente"
else
    echo "   ❌ Error instalando dependencias"
fi

# 3. Configurar permisos
echo ""
echo "3. Configurando permisos..."
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
echo "   ✅ Permisos configurados"

# 4. Limpiar cache
echo ""
echo "4. Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo "   ✅ Cache limpiado"

# 5. Ejecutar migraciones
echo ""
echo "5. Ejecutando migraciones..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo "   ✅ Migraciones ejecutadas correctamente"
else
    echo "   ❌ Error en migraciones"
fi

# 6. Optimizar para producción
echo ""
echo "6. Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "   ✅ Optimización completada"

# 7. Verificar sintaxis PHP
echo ""
echo "7. Verificando sintaxis PHP..."
find app -name "*.php" -exec php -l {} \; | grep -v "No syntax errors"
if [ $? -eq 0 ]; then
    echo "   ✅ Sintaxis PHP correcta"
else
    echo "   ❌ Errores de sintaxis encontrados"
fi

# 8. Crear storage link si no existe
echo ""
echo "8. Verificando storage link..."
if [ ! -L "public/storage" ]; then
    php artisan storage:link
    echo "   ✅ Storage link creado"
else
    echo "   ✅ Storage link ya existe"
fi

# 9. Verificar .env
echo ""
echo "9. Verificando archivo .env..."
if [ -f ".env" ]; then
    echo "   ✅ .env existe"
    if grep -q "APP_ENV=production" .env; then
        echo "   ✅ APP_ENV configurado para producción"
    else
        echo "   ⚠️  Considera cambiar APP_ENV a 'production'"
    fi
    if grep -q "APP_DEBUG=false" .env; then
        echo "   ✅ APP_DEBUG desactivado para producción"
    else
        echo "   ⚠️  Considera cambiar APP_DEBUG a 'false'"
    fi
else
    echo "   ❌ .env NO existe - Copia .env.example a .env y configura"
fi

echo ""
echo "=== REPARACIÓN COMPLETADA ==="
echo "Reinicia los servicios web si es necesario:"
echo "sudo systemctl restart nginx"
echo "sudo systemctl restart php8.1-fpm"
echo ""
echo "Si los problemas persisten, revisa los logs:"
echo "tail -f storage/logs/laravel.log"

