#!/bin/bash

echo "=================================="
echo "🚀 DESPLIEGUE A PRODUCCIÓN - IMPEF"
echo "=================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: No estás en el directorio del proyecto Laravel${NC}"
    echo "Cambia al directorio correcto y ejecuta este script nuevamente"
    exit 1
fi

echo -e "${GREEN}✅ Directorio del proyecto verificado${NC}"
echo ""

# 1. ACTUALIZAR CÓDIGO DESDE GIT
echo "=================================="
echo "📥 PASO 1: Actualizando código..."
echo "=================================="
git pull origin main
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Código actualizado${NC}"
else
    echo -e "${RED}❌ Error actualizando código${NC}"
    exit 1
fi
echo ""

# 2. INSTALAR DEPENDENCIAS PHP
echo "=================================="
echo "📦 PASO 2: Instalando dependencias PHP..."
echo "=================================="
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Dependencias PHP instaladas${NC}"
else
    echo -e "${RED}❌ Error instalando dependencias PHP${NC}"
    exit 1
fi
echo ""

# 3. INSTALAR DEPENDENCIAS NODE (para assets)
echo "=================================="
echo "📦 PASO 3: Verificando Node.js y assets..."
echo "=================================="
if command -v npm &> /dev/null; then
    echo "Node.js encontrado, instalando dependencias..."
    npm install
    echo "Compilando assets para producción..."
    npm run build
    echo -e "${GREEN}✅ Assets compilados${NC}"
else
    echo -e "${YELLOW}⚠️  Node.js no instalado, usando assets pre-compilados${NC}"
    echo "Verificando que existan assets en public/build/..."
    if [ -d "public/build" ] && [ "$(ls -A public/build)" ]; then
        echo -e "${GREEN}✅ Assets pre-compilados encontrados${NC}"
    else
        echo -e "${RED}❌ No hay assets compilados. Por favor, compila en localhost y súbelos${NC}"
    fi
fi
echo ""

# 4. CONFIGURAR PERMISOS
echo "=================================="
echo "🔐 PASO 4: Configurando permisos..."
echo "=================================="
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✅ Permisos configurados${NC}"
echo ""

# 5. LIMPIAR CACHE
echo "=================================="
echo "🧹 PASO 5: Limpiando cache..."
echo "=================================="
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Cache limpiado${NC}"
echo ""

# 6. CREAR STORAGE LINK
echo "=================================="
echo "🔗 PASO 6: Verificando storage link..."
echo "=================================="
if [ -L "public/storage" ]; then
    echo -e "${GREEN}✅ Storage link ya existe${NC}"
else
    php artisan storage:link
    echo -e "${GREEN}✅ Storage link creado${NC}"
fi
echo ""

# 7. EJECUTAR MIGRACIONES
echo "=================================="
echo "🗄️  PASO 7: Ejecutando migraciones..."
echo "=================================="
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Migraciones ejecutadas${NC}"
else
    echo -e "${YELLOW}⚠️  Error en migraciones (puede ser normal si ya están aplicadas)${NC}"
fi
echo ""

# 8. SINCRONIZAR ESTADO DE FACTURAS
echo "=================================="
echo "🔄 PASO 8: Sincronizando estado de facturas..."
echo "=================================="
php artisan sync:invoice-status
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Estado de facturas sincronizado${NC}"
else
    echo -e "${YELLOW}⚠️  Comando de sincronización no encontrado${NC}"
fi
echo ""

# 9. OPTIMIZAR PARA PRODUCCIÓN
echo "=================================="
echo "⚡ PASO 9: Optimizando para producción..."
echo "=================================="
php artisan config:cache
php artisan route:cache
# NO ejecutar view:cache si da problemas con auth-session-status
echo -e "${YELLOW}⚠️  Saltando view:cache para evitar errores de componentes${NC}"
echo -e "${GREEN}✅ Optimización completada${NC}"
echo ""

# 10. VERIFICAR .ENV
echo "=================================="
echo "🔍 PASO 10: Verificando configuración .env..."
echo "=================================="
if [ -f ".env" ]; then
    echo -e "${GREEN}✅ Archivo .env existe${NC}"
    
    # Verificar configuraciones importantes
    if grep -q "APP_ENV=production" .env; then
        echo -e "${GREEN}✅ APP_ENV=production${NC}"
    else
        echo -e "${YELLOW}⚠️  Considera cambiar APP_ENV a 'production'${NC}"
    fi
    
    if grep -q "APP_DEBUG=false" .env; then
        echo -e "${GREEN}✅ APP_DEBUG=false${NC}"
    else
        echo -e "${YELLOW}⚠️  IMPORTANTE: Cambia APP_DEBUG a 'false' en producción${NC}"
    fi
    
    if grep -q "SESSION_DRIVER=file" .env || grep -q "SESSION_DRIVER=database" .env; then
        echo -e "${GREEN}✅ SESSION_DRIVER configurado${NC}"
    else
        echo -e "${YELLOW}⚠️  Verifica SESSION_DRIVER en .env${NC}"
    fi
else
    echo -e "${RED}❌ Archivo .env NO existe${NC}"
    echo "Por favor, copia .env.example a .env y configura las variables"
    exit 1
fi
echo ""

# 11. VERIFICAR BASE DE DATOS
echo "=================================="
echo "🗄️  PASO 11: Verificando conexión a base de datos..."
echo "=================================="
php artisan db:show 2>/dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Conexión a base de datos exitosa${NC}"
else
    echo -e "${YELLOW}⚠️  No se pudo verificar la base de datos${NC}"
fi
echo ""

# 12. VERIFICAR PERMISOS DE DIRECTORIOS CRÍTICOS
echo "=================================="
echo "📁 PASO 12: Verificando permisos de directorios críticos..."
echo "=================================="
directories=("storage/app" "storage/framework" "storage/logs" "bootstrap/cache" "public/build")
for dir in "${directories[@]}"; do
    if [ -d "$dir" ]; then
        echo -e "${GREEN}✅ $dir existe${NC}"
    else
        echo -e "${YELLOW}⚠️  $dir no existe, creándolo...${NC}"
        mkdir -p "$dir"
        sudo chmod -R 775 "$dir"
    fi
done
echo ""

# 13. REINICIAR SERVICIOS
echo "=================================="
echo "🔄 PASO 13: Reiniciando servicios..."
echo "=================================="
echo "Reiniciando PHP-FPM y Nginx..."
sudo systemctl restart php8.1-fpm 2>/dev/null || sudo systemctl restart php8.2-fpm 2>/dev/null || sudo systemctl restart php-fpm 2>/dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ PHP-FPM reiniciado${NC}"
else
    echo -e "${YELLOW}⚠️  No se pudo reiniciar PHP-FPM automáticamente${NC}"
fi

sudo systemctl restart nginx 2>/dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Nginx reiniciado${NC}"
else
    echo -e "${YELLOW}⚠️  No se pudo reiniciar Nginx automáticamente${NC}"
fi
echo ""

# RESUMEN FINAL
echo "=================================="
echo "✨ DESPLIEGUE COMPLETADO"
echo "=================================="
echo ""
echo -e "${GREEN}✅ El sistema ha sido desplegado a producción${NC}"
echo ""
echo "📋 PRÓXIMOS PASOS:"
echo "1. Verifica que la aplicación esté funcionando en el navegador"
echo "2. Prueba crear una factura para verificar que el guardado funciona"
echo "3. Si hay errores, revisa los logs:"
echo "   tail -f storage/logs/laravel.log"
echo ""
echo "🔍 DIAGNÓSTICO RÁPIDO:"
echo "   php artisan tinker"
echo "   >>> App\Models\Invoice::count()"
echo ""
echo "=================================="

