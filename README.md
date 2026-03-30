Клонируйте репозиторий:
bash
git clone <url-репозитория> shortener
cd shortener
Скопируйте файл окружения:
bash
cp .env.example .env

При необходимости отредактируйте .env (пароли, имя БД и т.д.).

Убедитесь, что структура проекта содержит все необходимые файлы:
Dockerfile
docker-compose.yml
entrypoint.sh
nginx.conf
.env
Папки controllers/, models/, views/, migrations/, services/, repositories/ с вашим кодом.

Запустите Docker-контейнеры:

bash
docker-compose up -d --build

При первом запуске автоматически выполнятся: установка зависимостей Composer (composer install), миграции, ожидание готовности базы данных.
Контейнеры app, web, db будут запущены в фоне.

Проверьте работу:

Откройте в браузере http://localhost:8080.
Введите любой URL (например, https://ya.ru) и нажмите «ОК».
Должны появиться короткая ссылка и QR-код.
При переходе по короткой ссылке произойдёт редирект на исходный сайт, а в базе увеличится счётчик переходов и запишется IP.