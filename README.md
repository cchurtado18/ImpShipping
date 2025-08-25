# IMPEF - Sistema de Gestión de Encomiendas

Sistema web MVP para una agencia que envía encomiendas de EE. UU. a Nicaragua, con una ruta al mes.

## Stack Tecnológico

- **Backend**: Laravel 11 (PHP 8.2+)
- **Base de Datos**: MySQL 8
- **Frontend**: Blade + Livewire + TailwindCSS
- **Storage**: Local (dev) / S3 (prod)

## Paquetes Instalados

- `livewire/livewire` - Componentes reactivos
- `spatie/laravel-activitylog` - Auditoría de cambios
- `maatwebsite/excel` - Import/Export Excel/CSV
- `barryvdh/laravel-dompdf` - Generación de PDFs
- `simplesoftwareio/simple-qrcode` - Generación de códigos QR
- `spatie/laravel-backup` - Backups automáticos

## Instalación

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd IMPEF
```

2. **Configurar variables de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configurar base de datos en .env**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=impef
DB_USERNAME=root
DB_PASSWORD=
```

4. **Instalar dependencias**
```bash
composer install
npm install
```

5. **Ejecutar migraciones y seeders**
```bash
php artisan migrate --seed
```

6. **Crear enlace simbólico para storage**
```bash
php artisan storage:link
```

7. **Compilar assets**
```bash
npm run build
```

8. **Iniciar servidor**
```bash
php artisan serve
```

## Configuración de Backups

### Automático (Recomendado)
El sistema está configurado para ejecutar backups automáticamente a las 02:00 AM:

```bash
# Verificar que el cron esté configurado
crontab -e

# Agregar esta línea:
0 2 * * * cd /path/to/your/project && php artisan backup:run >> /dev/null 2>&1
```

### Manual
```bash
php artisan backup:run
```

### Alternativa con mysqldump
```bash
mysqldump -u root -p impef > backup_$(date +%Y%m%d_%H%M%S).sql
```

## Roles y Permisos

- **Admin**: Acceso completo, puede cerrar rutas y eliminar pagos
- **Operator**: Acceso limitado, no puede cerrar rutas ni eliminar pagos

## Funcionalidades Principales

### 1. Gestión de Rutas Mensuales
- Creación automática de rutas del mes
- Estados: planning, collecting, in_transit, arrived, closed
- Fechas configurables de corte y salida

### 2. Gestión de Envíos
- CRUD completo de envíos
- Códigos únicos automáticos (formato: SG-YYYYMM-XXXXXX)
- Estados: lead, ready, delivered, cancelled
- Cálculo automático de precios según vigencia

### 3. Sistema de Pagos
- Registro de pagos por envío
- Estados de cobro: pending, partial, paid
- Métodos de pago configurables

### 4. Control de Gastos
- Categorías: fuel, freight, warehouse, taxes, toll, per_diem, last_mile, other
- Registro de gastos por ruta
- Cálculo de utilidades

### 5. Import/Export
- Importación masiva desde CSV/Excel
- Exportación de reportes en Excel y PDF
- Plantilla de importación incluida

### 6. Tracking Público
- Página pública de seguimiento: `/t/{code}`
- Sin información personal (PII)
- Códigos QR para cada envío

### 7. Auditoría
- Registro automático de cambios en envíos, pagos y rutas
- Historial completo de modificaciones

## Estructura de Archivos

```
app/
├── Console/Commands/BackupCommand.php
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── RoutesController.php
│   ├── TrackingController.php
│   ├── ShipmentController.php
│   ├── PaymentController.php
│   ├── RouteExpenseController.php
│   └── RouteController.php
├── Livewire/
│   ├── ShipmentsTable.php
│   ├── ShipmentFormModal.php
│   ├── RouteExpensesTable.php
│   ├── QuickPaymentModal.php
│   └── RouteSummary.php
├── Models/
│   ├── User.php
│   ├── Box.php
│   ├── PriceList.php
│   ├── Route.php
│   ├── Client.php
│   ├── Recipient.php
│   ├── Shipment.php
│   ├── Payment.php
│   ├── RouteExpense.php
│   ├── Setting.php
│   └── ExchangeRate.php
├── Policies/
│   ├── PaymentPolicy.php
│   └── RoutePolicy.php
├── Providers/
│   └── AuthServiceProvider.php
└── Services/
    └── MonthlyRouteService.php

resources/views/
├── layouts/app.blade.php
├── dashboard.blade.php
├── routes/current.blade.php
├── tracking/show.blade.php
└── livewire/
    ├── shipments-table.blade.php
    ├── shipment-form-modal.blade.php
    ├── route-expenses-table.blade.php
    ├── quick-payment-modal.blade.php
    └── route-summary.blade.php
```

## Checklist de Validación MVP

- [ ] Migrate & seed ok
- [ ] Ruta del mes ok
- [ ] CRUD envíos/gastos/pagos ok
- [ ] Resumen con utilidades ok
- [ ] Import CSV ok
- [ ] Export Excel/PDF ok
- [ ] Tracking público ok
- [ ] Auditoría y backups ok

## Comandos Útiles

```bash
# Crear usuario admin
php artisan tinker
User::create(['name' => 'Admin', 'email' => 'admin@impef.com', 'password' => Hash::make('password'), 'role' => 'admin']);

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ver logs
tail -f storage/logs/laravel.log

# Ejecutar tests
php artisan test
```

## Configuración de Producción

1. **Variables de entorno**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

2. **Storage S3**
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

3. **Optimización**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Soporte

Para soporte técnico o preguntas, contacte al equipo de desarrollo.
