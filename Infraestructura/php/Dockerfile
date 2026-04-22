# PHP-FPM: PHP como proceso independiente
FROM php:8.2-fpm

# Instalar extensiones para conectar con MariaDB
# pdo y pdo_mysql: necesarios para usar PDO con MySQL/MariaDB
# mysqli: extension alternativa, la incluimos por compatibilidad
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN ln -sf /usr/share/zoneinfo/Europe/Madrid /etc/localtime
# Directorio donde estara el codigo PHP
# Este directorio se monta como volumen desde el docker-compose
WORKDIR /var/www/html

# Puerto donde escucha PHP-FPM para recibir peticiones de Apache
EXPOSE 9000
