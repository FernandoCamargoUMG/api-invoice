FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite para rutas limpias
RUN a2enmod rewrite

# Copiar proyecto al contenedor
COPY . /var/www/html/

# Apuntar Apache a la carpeta public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html/

EXPOSE 80
