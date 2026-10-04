# Etapa 1: dependencias de producción
FROM composer:2 AS dependencias
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader
COPY src/ src/
RUN composer dump-autoload --optimize --no-dev

# Etapa 2: servidor web
FROM php:8.2-apache
LABEL org.opencontainers.image.title="sistema-inventario" \
      org.opencontainers.image.description="Sistema de Control de Inventario"

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html
COPY --from=dependencias /app/vendor/ vendor/
COPY src/ src/
COPY public/ public/

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s CMD curl -f http://localhost/ || exit 1
