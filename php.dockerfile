FROM php:5.6

# Adiciona o instalador de extensões PHP
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Instala as extensões PHP necessárias
RUN install-php-extensions zip gd imagick

# Instala o Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# Instala o pngquant e o ImageMagick
RUN apt-get update && apt-get install -y \
    pngquant \
    imagemagick \
    && rm -rf /var/lib/apt/lists/*

# Copia a configuração personalizada do PHP
COPY ./php.ini /usr/local/etc/php/conf.d/php.ini

# Define o diretório de trabalho
WORKDIR /app

# Comando padrão ao iniciar o contêiner
CMD ["php", "-S", "0.0.0.0:8001", "-t", "public"]
