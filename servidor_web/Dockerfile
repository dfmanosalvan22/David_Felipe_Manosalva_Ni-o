# Apache sin PHP integrado
# PHP corre en su propio contenedor (logitrans-php)
FROM httpd:2.4

# Habilitar modulos necesarios para reenviar peticiones PHP
# proxy_module: permite usar Apache como proxy
# proxy_fcgi_module: permite comunicarse con PHP-FPM por FastCGI
RUN sed -i 's/#LoadModule proxy_module/LoadModule proxy_module/' \
    /usr/local/apache2/conf/httpd.conf
RUN sed -i 's/#LoadModule proxy_fcgi_module/LoadModule proxy_fcgi_module/' \
    /usr/local/apache2/conf/httpd.conf

# Copiar la configuracion del sitio web
COPY ./httpd-vhost.conf /usr/local/apache2/conf/extra/httpd-vhost.conf

# Incluir la configuracion en el archivo principal de Apache
RUN echo "Include conf/extra/httpd-vhost.conf" >> \
    /usr/local/apache2/conf/httpd.conf

EXPOSE 80
