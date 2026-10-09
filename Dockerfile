FROM php:8.2-apache

# Extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite para las rutas de Mapachi
RUN a2enmod rewrite

# Copiar el proyecto al directorio público de Apache
COPY . /var/www/html/

# Configurar Apache para permitir las reglas de .htaccess
RUN sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Permisos básicos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80