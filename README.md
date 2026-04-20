# Каталог товаров (Laravel + Vue + Inertia)

Тестовое задание: REST API, публичный каталог, админка на **Vue 3 (Composition API)** и **Inertia.js**, UI — **Element Plus**, БД — **PostgreSQL**, аутентификация мутаций — **Laravel Sanctum** (токен).

## Стек

- PHP **8.2**, Laravel **12**
- Vue **3**, Inertia **3**, Vite **7**, Tailwind **4**, Element Plus
- PostgreSQL **16** (в Docker)

## Быстрый старт (Docker)

```bash
cp .env.example .env
# В .env задайте APP_KEY: docker compose run --rm app php artisan key:generate

docker compose up -d postgres app
docker compose run --rm app php artisan migrate --seed
```

Приложение: **http://localhost:8000**  
Vite (разработка): **http://localhost:5173** (сервис `node` в `docker-compose.yml`).

Сборка фронта для продакшена:

```bash
docker compose run --rm node npm ci
docker compose run --rm node npm run build
```

## Учётная запись администратора

После сидов:

- Email: `admin@catalog.test`
- Пароль: `password`

Токен для API выдаётся через `POST /api/login` (email + password).

## API (кратко)

| Метод | Путь | Доступ |
|--------|------|--------|
| GET | `/api/products` | публично (пагинация, фильтр `category_id`, поиск `q`) |
| GET | `/api/products/{id}` | публично |
| POST/PUT/PATCH/DELETE | `/api/products` … | `Authorization: Bearer <token>` |
| GET | `/api/categories` | публично |
| POST | `/api/login` | публично |
| POST | `/api/logout` | токен |

Товары используют **мягкое удаление** (`softDeletes`).

## Тесты

```bash
docker compose run --rm app php artisan test
```

Используется SQLite в памяти (`phpunit.xml`).

## Лицензия

MIT (как у Laravel).
