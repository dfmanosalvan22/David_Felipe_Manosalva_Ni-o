# Imagen base oficial de HAProxy basada en Debian
FROM haproxy:2.9

# Copiar nuestra configuracion personalizada
COPY haproxy.cfg /usr/local/etc/haproxy/haproxy.cfg

# Puerto 80: trafico web
# Puerto 8080: panel de estadisticas
EXPOSE 80 8080
