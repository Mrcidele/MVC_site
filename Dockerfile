FROM php:8.4-apache

# 1. Instalar extensões necessárias para a base de dados
RUN docker-php-ext-install pdo pdo_mysql

# 2. Ativar o módulo de reescrita do Apache (Obrigatório para o .htaccess e o Router funcionarem)
RUN a2enmod rewrite

# 3. Instalar o Composer dentro do contentor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Mudar a raiz do site (DocumentRoot) diretamente para a pasta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/src/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html