# Imagen oficial de MariaDB
FROM mariadb:11.2

# El script inicio.sql se ejecuta automaticamente la primera vez
# que arranca el contenedor y el volumen esta vacio
# Crea las tablas e inserta los datos iniciales
COPY inicio.sql /docker-entrypoint-initdb.d/

EXPOSE 3306
