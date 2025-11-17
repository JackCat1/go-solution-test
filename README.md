## Library REST API (Yii2)

Мини‑сервис для учёта книг. Yii2 Basic + SQLite + JWT, все ответы в JSON.

### Требования

- PHP 7.4+ (лучше 8.x)
- Composer
- Расширение `pdo_sqlite`


### Установка и запуск


1) Установить зависимости

```bash
composer install
```

2) Изменить настройки

По умолчанию используется база данных SQLite если нужно использовать MySql то config/db.php нужно изменить настройки на :

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=example',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];
```

В config/params.php нужно задать секрет для JWT токенов (jwtSecret)

3) Выполнить миграции
  
```bash
php yii migrate --interactive=0
```

4) (Опционально) Чтобы заполнить БД тестовыми данными выполните команду

```bash
php yii fixture/load "*" --interactive=0
```

5) Запуск тестового сервера

```bash
php yii serve
```

По умолчанию сервер запустится на http://127.0.0.1:8080


### Доступные эндпоинты
Пользователи:

POST /users — регистрация нового пользователя (логин, пароль, email).

POST /auth/login — авторизация (получение JWT токена).

GET /users/{id} — просмотр профиля (только для авторизованных).

Книги:

GET /books — список всех книг (с пагинацией).

POST /books — добавить книгу (только авторизованный пользователь).

GET /books/{id} — получить информацию о книге.

PUT /books/{id} — обновить данные книги (только авторизованный).

DELETE /books/{id} — удалить книгу (только авторизованный).

Доступна коллекция Postman (файл go-solution-test.postman_collection.json)

### Тесты
Для основных методов контроллеров доступны тесты. Запуск

```bash
vendor/bin/codecept run
```