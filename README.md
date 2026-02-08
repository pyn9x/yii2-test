
# Yii2 Book Catalog

Yii2 basic (Docker) с каталогом авторов/книг, подписками и эмуляцией SMS.

## Требования
- Docker + Docker Compose v2

## Запуск
```bash
cp .env.example .env
make up              # старт php+mysql
make migrate         # миграции
make fixtures        # загрузить демо-данные
```
Открыть: http://localhost:8000

### Демо-учётки
- user / user123

## Функциональность
- Роли: guest (чтение), user (CRUD авторов/книг, подписка/отписка).
- Подписки на авторов, SMS-эмуляция `app\components\SmsPilotEmulator` (лог в `sms_log`) при создании книги и обновлении автора.
- Отчет TOP-10 авторов по подписчикам (тай-брейк по числу книг).

## Полезные команды
```bash
make logs    # логи контейнеров
make bash    # shell в php-контейнере
make down    # стоп и очистка volume
```

## Настройки
- Параметры БД и APP_KEY читаются из `.env`/`.env.example`.
- `config/db.php` использует переменные окружения.
