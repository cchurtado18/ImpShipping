# 🚀 Despliegue de IMPEF en DigitalOcean

## 💰 **Costo Estimado**

| Componente | Costo Mensual | Descripción |
|------------|---------------|-------------|
| **Droplet Básico** | $6/mes | 1GB RAM, 1 CPU, 25GB SSD |
| **Droplet Mejorado** | $12/mes | 2GB RAM, 1 CPU, 50GB SSD |
| **Dominio** | $12-15/año | Opcional pero recomendado |
| **Total Básico** | **~$6-7/mes** | Incluye dominio anual |
| **Total Mejorado** | **~$12-13/mes** | Incluye dominio anual |

## 📋 **Pasos para el Despliegue**

### **Opción 1: Despliegue Automático (Recomendado)**

1. **Crear Droplet en DigitalOcean**
   - Ir a [DigitalOcean](https://digitalocean.com)
   - Crear nuevo Droplet
   - Seleccionar Ubuntu 22.04 LTS
   - Plan: Basic ($6/mes)
   - Ubicación: NYC3 (para EE.UU.)

2. **Ejecutar Script de Despliegue**
   ```bash
   # Conectar al servidor
   ssh root@TU_IP_DEL_SERVIDOR
   
   # Descargar y ejecutar script
   wget https://raw.githubusercontent.com/tu-usuario/impef/main/deploy.sh
   chmod +x deploy.sh
   ./deploy.sh
   ```

3. **Subir Aplicación**
   ```bash
   # Desde tu máquina local
   # Editar upload-to-server.sh y cambiar TU_IP_DEL_SERVIDOR
   nano upload-to-server.sh
   
   # Ejecutar script de subida
   ./upload-to-server.sh
   ```

### **Opción 2: Despliegue Manual**

Seguir la guía completa en `deployment-guide.md`

## 🔧 **Configuración Post-Despliegue**

### **1. Configurar Dominio (Opcional)**

Si tienes un dominio:

```bash
# En el servidor
nano /etc/nginx/sites-available/impef
# Cambiar server_name _; por server_name tu-dominio.com;

# Configurar SSL
apt install -y certbot python3-certbot-nginx
certbot --nginx -d tu-dominio.com
```

### **2. Configurar Backup Automático**

El script ya configura backup diario automático en `/root/backup.sh`

### **3. Monitoreo**

```bash
# Ver logs de la aplicación
tail -f /var/www/impef/storage/logs/laravel.log

# Ver logs de Nginx
tail -f /var/log/nginx/error.log

# Ver estado de servicios
systemctl status nginx php8.2-fpm mysql
```

## 📊 **Especificaciones del Servidor**

### **Droplet Básico ($6/mes)**
- **CPU**: 1 vCPU
- **RAM**: 1GB
- **SSD**: 25GB
- **Transferencia**: 1TB
- **Ideal para**: Desarrollo, pruebas, tráfico bajo

### **Droplet Mejorado ($12/mes)**
- **CPU**: 1 vCPU
- **RAM**: 2GB
- **SSD**: 50GB
- **Transferencia**: 2TB
- **Ideal para**: Producción, tráfico medio

## 🔒 **Seguridad**

### **Configurado Automáticamente:**
- ✅ Firewall (UFW) habilitado
- ✅ Solo puertos SSH y HTTP/HTTPS abiertos
- ✅ MySQL configurado de forma segura
- ✅ Permisos de archivos correctos
- ✅ Headers de seguridad en Nginx

### **Recomendaciones Adicionales:**
- 🔑 Usar SSH keys en lugar de contraseñas
- 🔄 Mantener el sistema actualizado
- 📝 Revisar logs regularmente
- 💾 Hacer backups manuales adicionales

## 🚨 **Troubleshooting**

### **Problemas Comunes:**

| Problema | Solución |
|----------|----------|
| **Error 502** | `systemctl restart php8.2-fpm` |
| **Error 500** | Revisar logs: `tail -f /var/www/impef/storage/logs/laravel.log` |
| **Error de permisos** | `chown -R www-data:www-data /var/www/impef` |
| **Error de base de datos** | Verificar conexión MySQL |
| **Página en blanco** | Verificar logs de PHP-FPM |

### **Comandos Útiles:**

```bash
# Reiniciar todos los servicios
systemctl restart nginx php8.2-fpm mysql

# Ver logs en tiempo real
tail -f /var/log/nginx/error.log /var/www/impef/storage/logs/laravel.log

# Verificar configuración de Nginx
nginx -t

# Verificar estado de servicios
systemctl status nginx php8.2-fpm mysql

# Ejecutar backup manual
/root/backup.sh
```

## 📈 **Escalabilidad**

### **Cuándo Actualizar el Droplet:**
- **CPU alta**: Actualizar a 2 vCPU
- **RAM insuficiente**: Actualizar a 2GB o 4GB
- **Espacio en disco**: Actualizar a 50GB o 100GB
- **Mucho tráfico**: Considerar Load Balancer

### **Opciones de Escalado:**
1. **Vertical**: Actualizar el Droplet actual
2. **Horizontal**: Agregar más Droplets con Load Balancer
3. **CDN**: Usar Cloudflare para contenido estático

## 💡 **Optimizaciones**

### **Ya Configuradas:**
- ✅ PHP optimizado para producción
- ✅ Nginx configurado para Laravel
- ✅ Cache de configuración, rutas y vistas
- ✅ Logrotate configurado
- ✅ Backup automático

### **Opcionales:**
- 🔄 Redis para cache (mejora rendimiento)
- 📊 Monitoreo con herramientas como New Relic
- 🚀 CDN para assets estáticos
- 🔍 Logs centralizados

## 📞 **Soporte**

### **Recursos Útiles:**
- 📚 [Documentación de Laravel](https://laravel.com/docs)
- 🌐 [Documentación de DigitalOcean](https://docs.digitalocean.com)
- 🔧 [Guía de Nginx](https://nginx.org/en/docs/)
- 🐘 [Documentación de PHP](https://www.php.net/docs.php)

### **Comandos de Emergencia:**

```bash
# Restaurar desde backup
mysql -u impef_user -p impef_db < /root/backups/db_backup_YYYYMMDD_HHMMSS.sql

# Restaurar archivos
tar -xzf /root/backups/app_backup_YYYYMMDD_HHMMSS.tar.gz -C /var/www/

# Reiniciar todo el servidor
reboot
```

---

## ✅ **Checklist de Despliegue**

- [ ] Crear Droplet en DigitalOcean
- [ ] Ejecutar script de despliegue
- [ ] Subir aplicación al servidor
- [ ] Configurar dominio (opcional)
- [ ] Configurar SSL (opcional)
- [ ] Probar aplicación
- [ ] Configurar backup automático
- [ ] Documentar credenciales
- [ ] Configurar monitoreo

**¡Tu aplicación IMPEF estará lista para producción! 🎉**
