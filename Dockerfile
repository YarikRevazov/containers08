FROM php:7.4-fpm as base

# Обновляем пакеты и устанавливаем необходимые зависимости
RUN apt-get update && \
    apt-get install -y sqlite3 libsqlite3-dev && \
    docker-php-ext-install pdo_sqlite

# Устанавливаем том для базы данных
VOLUME ["/var/www/db"]

# Копируем схему базы данных
COPY sql/schema.sql /var/www/db/schema.sql

# Подготавливаем базу данных
RUN echo "prepare database" && \
    cat /var/www/db/schema.sql | sqlite3 /var/www/db/db.sqlite && \
    chmod 777 /var/www/db/db.sqlite && \
    rm -rf /var/www/db/schema.sql && \
    echo "database is ready"

# Копируем содержимое папки site в контейнер
COPY site /var/www/html
