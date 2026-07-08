# Blogy

Простой блог на чистом PHP 8.1 с шаблонизатором Smarty и MySQL. Без фреймворков.

## Возможности

- Главная: категории, в которых есть статьи, по 3 последних поста в каждой + ссылка «Все статьи».
- Страница категории: название, описание, список статей, сортировка (по дате / по просмотрам), пагинация.
- Страница статьи: полная информация, счётчик просмотров, блок из 3 похожих статей.
- Статья может входить в одну или несколько категорий (связь many-to-many).
- Сидинг категорий и статей одной командой.

## Стек

- PHP 8.1 (PDO)
- Smarty 5
- MySQL 8
- Smarty + Composer (автозагрузка PSR-4)
- SCSS → CSS
- Docker / docker-compose

## Структура

```
public/            корень веб-сервера (front-controller, .htaccess, ассеты)
src/               PHP-код (Database, Router, View, Models, Controllers)
templates/         шаблоны Smarty
templates_c/       скомпилированные шаблоны (генерируются)
scss/              исходники стилей
db/                schema.sql и сидер
```

## Запуск через Docker

```bash
docker compose up -d --build
docker compose exec app php db/seed.php
```

Сайт: http://localhost:8080

Схема БД применяется автоматически при первом старте контейнера MySQL
(`db/schema.sql` через `docker-entrypoint-initdb.d`). Сидер наполняет базу
демо-данными.

## Пересборка стилей

```bash
npm install
npm run build:css
```

## Локальный запуск без Docker

Нужны PHP 8.1+, MySQL и Composer.

```bash
composer install
mysql -u root -p < db/schema.sql
DB_HOST=127.0.0.1 DB_NAME=blog DB_USER=blog DB_PASS=blog php db/seed.php
php -S localhost:8080 -t public
```

Параметры подключения читаются из переменных окружения
`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
