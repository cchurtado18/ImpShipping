<?php

/**
 * Script de Diagnóstico del Servidor
 * Ejecutar en el servidor para identificar problemas
 */

echo "=== DIAGNÓSTICO DEL SERVIDOR ===\n\n";

// 1. Verificar PHP
echo "1. PHP VERSION:\n";
echo "   Versión: " . PHP_VERSION . "\n";
echo "   Extensiones requeridas:\n";
$required_extensions = ['pdo', 'pdo_mysql', 'openssl', 'mbstring', 'tokenizer', 'xml', 'curl', 'gd', 'zip', 'fileinfo'];
foreach ($required_extensions as $ext) {
    echo "   - $ext: " . (extension_loaded($ext) ? "✅ OK" : "❌ FALTANTE") . "\n";
}
echo "\n";

// 2. Verificar Composer
echo "2. COMPOSER:\n";
if (file_exists('composer.json')) {
    echo "   ✅ composer.json encontrado\n";
} else {
    echo "   ❌ composer.json NO encontrado\n";
}

if (file_exists('vendor/autoload.php')) {
    echo "   ✅ vendor/autoload.php encontrado\n";
} else {
    echo "   ❌ vendor/autoload.php NO encontrado - Ejecutar: composer install\n";
}
echo "\n";

// 3. Verificar .env
echo "3. CONFIGURACIÓN:\n";
if (file_exists('.env')) {
    echo "   ✅ .env encontrado\n";
    $env_content = file_get_contents('.env');
    $required_env = ['APP_ENV', 'APP_DEBUG', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE'];
    foreach ($required_env as $key) {
        if (strpos($env_content, $key) !== false) {
            echo "   ✅ $key configurado\n";
        } else {
            echo "   ❌ $key NO configurado\n";
        }
    }
} else {
    echo "   ❌ .env NO encontrado\n";
}
echo "\n";

// 4. Verificar Storage
echo "4. STORAGE:\n";
$storage_paths = ['storage/app', 'storage/framework', 'storage/logs', 'bootstrap/cache'];
foreach ($storage_paths as $path) {
    if (is_dir($path) && is_writable($path)) {
        echo "   ✅ $path existe y es escribible\n";
    } else {
        echo "   ❌ $path NO existe o NO es escribible\n";
    }
}
echo "\n";

// 5. Verificar Base de Datos
echo "5. BASE DE DATOS:\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $pdo = DB::connection()->getPdo();
    echo "   ✅ Conexión a BD exitosa\n";
    
    // Verificar tablas principales
    $tables = ['users', 'clients', 'routes', 'shipments', 'invoices'];
    foreach ($tables as $table) {
        try {
            DB::table($table)->count();
            echo "   ✅ Tabla $table existe\n";
        } catch (Exception $e) {
            echo "   ❌ Tabla $table NO existe o tiene problemas\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error de conexión a BD: " . $e->getMessage() . "\n";
}
echo "\n";

// 6. Verificar Rutas
echo "6. RUTAS:\n";
try {
    $routes = Route::getRoutes();
    $route_count = count($routes);
    echo "   ✅ $route_count rutas cargadas\n";
    
    // Verificar rutas importantes
    $important_routes = ['routes.index', 'clients.index', 'invoices.index'];
    foreach ($important_routes as $route_name) {
        try {
            route($route_name);
            echo "   ✅ Ruta $route_name funciona\n";
        } catch (Exception $e) {
            echo "   ❌ Ruta $route_name tiene problemas\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error cargando rutas: " . $e->getMessage() . "\n";
}
echo "\n";

// 7. Verificar Livewire
echo "7. LIVEWIRE:\n";
$livewire_files = [
    'app/Livewire/RouteManager.php',
    'app/Livewire/ClientManager.php',
    'app/Livewire/AutoInvoiceForm.php'
];
foreach ($livewire_files as $file) {
    if (file_exists($file)) {
        echo "   ✅ $file existe\n";
    } else {
        echo "   ❌ $file NO existe\n";
    }
}
echo "\n";

echo "=== FIN DEL DIAGNÓSTICO ===\n";
echo "Si hay errores, ejecuta las correcciones sugeridas.\n";

