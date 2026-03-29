#!/bin/sh
set -e

# Если папка vendor отсутствует или пуста, устанавливаем зависимости
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "Running composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Убеждаемся, что дополнительные пакеты установлены (например, QR)
if ! composer show endroid/qr-code >/dev/null 2>&1; then
    echo "Installing extra dependencies..."
    composer require endroid/qr-code --no-interaction
fi

# Ожидание базы данных
echo "Waiting for database..."
MAX_TRIES=30
TRIES=0
until php -r "new PDO('mysql:host=$DB_HOST;dbname=$DB_NAME', '$DB_USER', '$DB_PASS');" 2>/dev/null; do
    TRIES=$((TRIES+1))
    if [ $TRIES -ge $MAX_TRIES ]; then
        echo "Database not available after $MAX_TRIES attempts. Exiting."
        exit 1
    fi
    sleep 2
    echo "Waiting for database... (${TRIES}/${MAX_TRIES})"
done

# Выполнение миграций
echo "Running migrations..."
php yii migrate --interactive=0

exec "$@"