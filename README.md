# LogiTrans S.A. — Proyecto Intermodular ASIR II

## Descripción del proyecto

Diseño, implementación y documentación de la infraestructura tecnológica completa de la empresa ficticia **LogiTrans S.A.**, una empresa de logística y transporte con sede en Mérida. El proyecto abarca la red corporativa interna, los servicios de red, la administración de servidores, una plataforma web pública accesible desde internet, seguridad perimetral, base de datos, backups automatizados y herramientas de administración.

---

## Tecnologías utilizadas

| Componente | Tecnología |
|---|---|
| Red corporativa | VirtualBox, Ubuntu Server 22.04, Windows Server 2022 |
| Balanceador de carga | HAProxy 2.9 |
| Servidores web | Apache 2.4 |
| Servidor de aplicación | PHP-FPM 8.2 |
| Base de datos | MariaDB 11.2 |
| Orquestación | Docker y Docker Compose |
| Servicios corporativos | DNS, DHCP, FTP con SSL (Windows Server 2022) |
| Directorio activo | Active Directory — dominio logitrans.local |
| Backups | Script Bash + interfaz Python/Tkinter vía SSH |
| Gestión de incidencias | Python con detección de roles por Active Directory |
| Frontend web | Bootstrap 5 |

---

## Arquitectura de red

La infraestructura implementa una arquitectura en cascada con seis segmentos de red:
```
Internet → Router → DMZ (Docker Host) → red_dmz → red_web → red_php → red_bd
```

| Segmento | Subred | Descripción |
|---|---|---|
| LAN corporativa | 192.168.1.0/24 | Empleados, Windows Server y router |
| DMZ | 192.168.50.0/24 | Docker host, único servidor expuesto |
| red_dmz | 192.168.10.0/24 | HAProxy — entrada de tráfico público |
| red_web | 192.168.20.0/24 | HAProxy → Apache x2 |
| red_php | 192.168.30.0/24 | Apache x2 → PHP-FPM |
| red_bd | 192.168.40.0/24 | PHP-FPM → MariaDB |

---

## Arrancar la infraestructura web
```bash
cd Infraestructura
docker-compose up -d --build
```

## Parar
```bash
docker-compose down
```

## Acceso

- **Aplicación web:** http://localhost
- **Panel HAProxy:** http://localhost:8080/stats
  - Usuario: `admin`
  - Contraseña: `logitrans2024`

## Comandos útiles
```bash
# Ver estado de los contenedores
docker-compose ps

# Ver logs de un contenedor
docker logs logitrans-balanceador
docker logs logitrans-web1
docker logs logitrans-php
docker logs logitrans-bd

# Entrar a la base de datos
docker exec -it logitrans-bd mariadb -u logitrans_user -p

# Reconstruir tras cambios
docker-compose up -d --build

# Borrar todo incluyendo volúmenes (borra los datos)
docker-compose down -v
```

---

## Servicios web disponibles

La plataforma web pública permite a los clientes de LogiTrans:

- Registrarse y autenticarse
- Solicitar los cuatro servicios de la empresa:
  - Transporte de mercancías
  - Almacenamiento en bodega
  - Transporte urgente
  - Logística integral
- Consultar el estado de sus solicitudes

---

## Documentación

La documentación técnica completa del proyecto está disponible en la carpeta `/documentacion`.
