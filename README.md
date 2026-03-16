# LogiTrans S.A. — Infraestructura Web

## Arquitectura

```
red_dmz (192.168.10.0/24)
    Balanceador HAProxy — IF1 entrada

red_web (192.168.20.0/24)
    Balanceador HAProxy — IF2 salida
    Servidor Web 1 (Apache) — IF1 entrada
    Servidor Web 2 (Apache) — IF1 entrada

red_php (192.168.30.0/24)
    Servidor Web 1 (Apache) — IF2 salida
    Servidor Web 2 (Apache) — IF2 salida
    Servidor PHP-FPM — IF1 entrada

red_bd (192.168.40.0/24)
    Servidor PHP-FPM — IF2 salida
    Base de Datos MariaDB
```

## Estructura de carpetas

```
logitrans/
├── docker-compose.yml
├── balanceador/
│   ├── Dockerfile
│   └── haproxy.cfg
├── servidor_web/
│   ├── Dockerfile
│   ├── httpd-vhost.conf
│   └── www/
│       ├── index.php
│       └── config/
│           └── bd.php
├── aplicacion/
│   └── Dockerfile
└── base_datos/
    ├── Dockerfile
    └── inicio.sql
```

## Comandos útiles

```bash
# Arrancar todo
docker-compose up -d

# Ver estado de los contenedores
docker-compose ps

# Ver logs de un contenedor
docker logs logitrans-balanceador
docker logs logitrans-web1
docker logs logitrans-php
docker logs logitrans-bd

# Parar todo (conserva los datos)
docker-compose down

# Parar todo y borrar los volúmenes (borra los datos)
docker-compose down -v

# Reconstruir tras cambios en Dockerfiles
docker-compose up -d --build

# Entrar a un contenedor
docker exec -it logitrans-bd mariadb -u logitrans_user -p
```

