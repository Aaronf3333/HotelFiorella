# Dockerfile para PHP + SQL Server

# Imagen base
FROM php:8.2-apache

# Evitar interacción durante instalación
ENV DEBIAN_FRONTEND=noninteractive

# Instalar dependencias necesarias
RUN apt-get update && \
    apt-get install -y \
        unixodbc-dev \
        gnupg2 \
        libgssapi-krb5-2 \
        curl \
        apt-transport-https \
        lsb-release \
        build-essential \
        unzip \
        && rm -rf /var/lib/apt/lists/*

# Agregar repositorio de Microsoft para drivers de SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > /etc/apt/trusted.gpg.d/microsoft.gpg && \
    curl https://packages.microsoft.com/config/debian/11/prod.list -o /etc/apt/sources.list.d/mssql-release.list

# Instalar msodbcsql18 y mssql-tools
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y \
        msodbcsql18 \
        mssql-tools \
        unixodbc-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP para SQL Server
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Copiar código de la aplicación al contenedor
COPY . /var/www/html/

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto 80
EXPOSE 80

# Comando por defecto de Apache
CMD ["apache2-foreground"]
