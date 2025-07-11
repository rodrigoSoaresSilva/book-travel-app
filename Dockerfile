FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    zip unzip curl git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY ./src /var/www

# Instala as dependências Laravel antes do entrypoint
RUN composer install --no-interaction

# Copia o script de inicialização
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh

# Define permissão de execução
RUN chmod +x /usr/local/bin/entrypoint.sh

# Define entrypoint e comando padrão
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
