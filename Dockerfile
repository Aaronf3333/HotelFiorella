# Usamos una imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para SQL Server
RUN apt-get update && \
    apt-get install -y unixodbc-dev gnupg2 libgssapi-krb5-2 curl && \
    curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - && \
    curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list && \
    apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools unixodbc-dev && \
    pecl install sqlsrv pdo_sqlsrv && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Copiar el código de tu proyecto al contenedor
COPY . /var/www/html/

# Dar permisos (opcional según tu proyecto)
RUN chown -R www-data:www-data /var/www/html/

# Exponer el puerto 80 para HTTP
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]
