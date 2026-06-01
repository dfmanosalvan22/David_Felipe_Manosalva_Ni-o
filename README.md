# LogiTrans S.A. — Proyecto Intermodular ASIR II

---

## Descripción del proyecto

Diseño, implementación y documentación de la infraestructura tecnológica completa de la empresa ficticia **LogiTrans S.A.**, empresa de logística y transporte con sede en Mérida. El proyecto abarca red corporativa, servicios de red, administración de servidores Windows y Linux, plataforma web pública desplegada en producción real, seguridad perimetral, base de datos, backups automatizados, gestión de incidencias, monitorización y gestión de activos TI.

---

## Componentes del proyecto

| Componente | Tecnología |
|---|---|
| Red corporativa | VirtualBox · Ubuntu Server 22.04 · Windows Server 2022 |
| Servicios corporativos | DNS · DHCP · FTPS con SSL (Windows Server 2022) |
| Directorio activo | Active Directory · dominio `logitrans.local` |
| Balanceador de carga | HAProxy 2.9 |
| Servidores web | Apache 2.4 x2 |
| Servidor de aplicación | PHP-FPM 8.2 |
| Base de datos | MariaDB 11.2 |
| Orquestación | Docker y Docker Compose |
| Frontend web | PHP 8.2 · Bootstrap 5 · Bootstrap Icons |
| Producción | VPS OVHcloud · Ubuntu 24.04 · dominio propio · SSL |
| Backups | Bash + Python/Tkinter + Paramiko · usuario restringido SSH |
| Gestión incidencias | Python · detección de roles por AD (`whoami /groups`) |
| Monitorización | CheckMK 2.3.0p18 · Tailscale · plugin Docker |
| Inventario y tickets | GLPI 10.0.16 · integrado con Active Directory |

---

## Arquitectura de red

La infraestructura implementa una arquitectura en cascada con seis segmentos de red:
```
Internet → Router → DMZ (Docker Host) → red_dmz → red_web → red_php → red_bd
```
| Segmento | Subred | Descripción |
|---|---|---|
| LAN corporativa | 192.168.1.0/24 | Empleados, Windows Server, GLPI, CheckMK |
| DMZ | 192.168.50.0/24 | Docker host de laboratorio |
| red_dmz | 192.168.10.0/24 | Entrada a HAProxy |
| red_web | 192.168.20.0/24 | HAProxy → Apache x2 |
| red_php | 192.168.30.0/24 | Apache x2 → PHP-FPM |
| red_bd | 192.168.40.0/24 | PHP-FPM → MariaDB (sin gateway) |

### Direccionamiento IP

| Servidor | IP | Rol |
|---|---|---|
| Windows Server | 192.168.1.10 | AD · DNS · DHCP · FTPS |
| GLPI | 192.168.1.50 | Gestión activos e incidencias |
| CheckMK | 192.168.1.60 | Monitorización |
| Docker Host (lab) | 192.168.50.10 | Stack Docker de laboratorio |
| VPS OVHcloud | 54.37.159.24 | Stack Docker en producción |

---

## Despliegue en laboratorio

### Requisitos
- Docker Engine y Docker Compose instalados
- Fichero `.env` con las credenciales en la carpeta `Infraestructura/`

### Variables de entorno necesarias (`.env`)
```env
MYSQL_ROOT_PASSWORD=[PASSWORD_ROOT]
MYSQL_DATABASE=[NOMBRE_BD]
MYSQL_USER=[USUARIO_BD]
MYSQL_PASSWORD=[PASSWORD_USUARIO]
```

### Arrancar
```bash
cd Infraestructura
docker compose up -d --build
```

### Parar
```bash
docker compose down
```

### Parar y borrar volúmenes (borra los datos)
```bash
docker compose down -v
```

### Accesos en laboratorio
| Servicio | URL |
|---|---|
| Aplicación web | http://localhost |
| Panel HAProxy Stats | http://localhost:8080/stats |

---

## Despliegue en producción (VPS)

La misma infraestructura está desplegada en una VPS OVHcloud con dominio propio y certificado SSL activo.

```bash
git clone https://github.com/dfmanosalvan22/David_Felipe_Manosalva_Ni-o.git
cd David_Felipe_Manosalva_Ni-o/Infraestructura
# Crear el fichero .env con las credenciales
docker compose up -d --build
```

**Medidas de seguridad aplicadas en la VPS:**
- UFW con solo puertos 80, 443 y 49152 abiertos
- Fail2ban con bloqueo tras 3 intentos SSH fallidos
- Puerto SSH cambiado a 49152
- Usuario `backup_servicio` restringido para backups

---

## Comandos útiles

```bash
# Ver estado de los contenedores
docker compose ps

# Ver logs de un contenedor
docker logs logitrans-balanceador
docker logs logitrans-web1
docker logs logitrans-web2
docker logs logitrans-php
docker logs logitrans-bd

# Entrar a la base de datos
docker exec -it logitrans-bd mariadb -u [USUARIO_BD] -p[PASSWORD_USUARIO]

# Reconstruir tras cambios
docker compose up -d --build

# Recargar solo la aplicación web (sin reiniciar BD)
docker compose restart logitrans-web1 logitrans-web2 logitrans-php
```

---

## Aplicación web

La plataforma web de LogiTrans tiene dos áreas:

### Área pública (clientes)
- Página principal con información de la empresa, servicios y flota desde BD
- Registro y autenticación de clientes
- Solicitud de servicios: transporte, almacenamiento, urgente, logística integral
- Panel personal con seguimiento de solicitudes y envíos
- Chat directo con el equipo de logística por cada solicitud

### Panel de empleados (`/admin/`)
Acceso restringido a puestos de logística (Jefe Logística, Mozo Almacén, Transportista, Jefe Conductores)
- Gestión de solicitudes con filtros por estado
- Aceptación con asignación de vehículo y conductor desde la BD
- Gestión de envíos con cambio de estado
- Chat con clientes por solicitud

---

## Scripts de administración

### Backups (`scripts/Backups.py`)
Interfaz gráfica Tkinter que conecta por SSH a la VPS y ejecuta backups remotos:
- **Backup SQL:** dump completo de `logitrans_db` con `mariadb-dump`
- **Backup Volumen:** compresión del volumen Docker de MariaDB

Requiere la clave privada `id_ed25519_backup` en la misma carpeta que el ejecutable.

### Incidencias (`scripts/Incidencias.py`)
Herramienta de terminal para gestión de incidencias corporativas. Detecta automáticamente el rol del usuario mediante `whoami /groups` comprobando pertenencia al grupo `GR_JEFES` del dominio.

---

## Servicios adicionales

### GLPI — Gestión TI
- **URL:** http://192.168.1.50
- Integrado con Active Directory `logitrans.local`
- Gestión de tickets con categorías: Hardware, Software, Red, Accesos
- Inventario completo de activos tecnológicos

### CheckMK — Monitorización
- **URL:** http://192.168.1.60/logitrans
- Monitoriza 5 hosts: Windows Server, GLPI, Router, Docker Host, VPS
- Conexión con la VPS mediante Tailscale (red privada virtual)
- Plugin Docker para monitorización de contenedores individuales

---

## Documentación

La documentación técnica completa está disponible en la carpeta `/documentacion/`.

Incluye arquitectura de red, configuración de todos los servicios, modelo de datos, seguridad, backups, monitorización, GLPI y la aplicación web. 
