## Chat App (PHP + MySQL + HTML/JS)

Minimal мессенджер: регистрация/вход, список чатов, сообщения, long‑polling.

### Установка
- Создайте БД и таблицы:
  - импортируйте файл `db.sql` (создаст БД `chat_app` и демо-данные)
- Настройте доступ к БД в `config.php` (DB_HOST/DB_USER/DB_PASS)
- Поместите проект в веб-корень (у вас `C:\OSPanel\domains\chat`)
- Откройте `http://chat/` в браузере

### Страницы и API
- UI: `index.php`
- Стили: `style.css`
- Auth: `api/auth.php` (POST action=register|login|logout, GET action=me)
- Чаты: `api/chats.php` (GET список, POST создание)
- Сообщения: `api/messages.php` (GET список, POST отправка)
- Long‑poll: `api/poll.php` (GET chat_id, after_id)

### Примечания
- Пароли хэшируются `password_hash` (bcrypt)
- Long‑pollинг ограничен `LPOLL_TIMEOUT` (см. `config.php`)
- Приложение использует `$_SESSION` для аутентификации

### TODO (опционально)
- Загрузка изображений (endpoint + `type=image`)
- Профили пользователей и поиск
- Вебсокеты вместо long‑poll


