FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite headers deflate expires

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# PHP config — production settings
RUN echo "expose_php = Off" >> /usr/local/etc/php/php.ini \
 && echo "session.cookie_httponly = 1" >> /usr/local/etc/php/php.ini \
 && echo "session.use_strict_mode = 1" >> /usr/local/etc/php/php.ini

# Apache config — allow .htaccess + set document root
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html|g' /etc/apache2/sites-available/000-default.conf
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
    Options -Indexes +FollowSymLinks\n\
</Directory>' >> /etc/apache2/sites-available/000-default.conf

# Copy project files
COPY . /var/www/html/

# Write permissions for JSON data files (products, orders, users)
RUN chown -R www-data:www-data /var/www/html/assets \
 && chmod -R 755 /var/www/html/assets \
 && chmod 664 /var/www/html/assets/products.json \
               /var/www/html/assets/orders.json \
               /var/www/html/assets/users.json \
               /var/www/html/assets/state.json \
               /var/www/html/assets/products-data.js

EXPOSE 80
