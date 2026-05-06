# Imagen base oficial de HAProxy basada en Debian
FROM haproxy:2.9

# Copiar nuestra configuracion personalizada
COPY haproxy.cfg /usr/local/etc/haproxy/haproxy.cfg

# Certificados (los montaremos desde host)
RUN mkdir -p /usr/local/etc/haproxy/certs

EXPOSE 80 443 8080
