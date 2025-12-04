# Установка и настройка Max API для Laravel

## 🚀 Быстрая установка

### 1. Регистрация Service Provider

Добавьте в `bootstrap/providers.php` (Laravel 11):

```php
return [
    App\Providers\MaxServiceProvider::class,
];
```

Или в `config/app.php` (Laravel 10):

```php
'providers' => [
    App\Providers\MaxServiceProvider::class,
],
```

### 2. Публикация конфигурации

```bash
php artisan vendor:publish --tag=max-config
```

Это создаст файл `config/max.php`.

### 3. Настройка .env

Добавьте в `.env`:

```env
MAX_BOT_TOKEN=your_bot_token_here
MAX_SECRET_KEY=your_secret_key_here
MAX_BOT_USERNAME=your_bot_username
MAX_WEBHOOK_URL="${APP_URL}/api/max/webhook"
MAX_MINI_APP_URL="${APP_URL}"

# Опционально
MAX_ADMIN_IDS=123456789,987654321
```

### 4. Загрузка helper функций

В `composer.json` добавьте:

```json
"autoload": {
    "files": [
        "app/Max/helpers.php"
    ]
}
```

Затем:

```bash
composer dump-autoload
```

### 5. Регистрация Middleware

В `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'max.auth' => \App\Http\Middleware\MaxAuth::class,
        'max.webhook' => \App\Http\Middleware\MaxWebhook::class,
        'max.admin' => \App\Http\Middleware\MaxAdmin::class,
    ]);
})
```

### 6. Настройка webhook

```bash
# Установить webhook
php artisan max:set-webhook

# Проверить статус
php artisan max:webhook-info

# Удалить webhook
php artisan max:delete-webhook

# Тест подключения
php artisan max:test
```

## 📚 Использование

### Helper функции (самый простой способ)

```php
// Отправить сообщение
max_send(123456789, 'Привет!');

// Валидировать Mini App
$isValid = max_validate_miniapp($params);
$user = max_get_user($params);

// Создать клавиатуру
$keyboard = max_keyboard()
    ->row()
    ->callback('Кнопка 1', 'btn1')
    ->callback('Кнопка 2', 'btn2')
    ->row()
    ->link('Сайт', 'https://example.com')
    ->get();

max_send($chatId, 'Выберите:', [
    'attachments' => [$keyboard]
]);
```

### Через Dependency Injection

```php
use App\Max\Bot;
use App\Max\MiniApp;

class MyController extends Controller
{
    public function __construct(
        protected Bot $bot,
        protected MiniApp $miniApp
    ) {}
    
    public function sendMessage()
    {
        $this->bot->sendMessage(123456789, 'Сообщение');
    }
}
```

### Через фасад

```php
use App\Max\Max;

Max::send(123456789, 'Сообщение');
Max::validateMiniApp($params);
```

## 🔒 Middleware

### MaxAuth - Аутентификация Mini App

```php
Route::middleware('max.auth')->group(function () {
    Route::post('/api/user/profile', [UserController::class, 'getProfile']);
});
```

Добавляет в request:
- `max_user` - данные пользователя
- `max_user_id` - ID пользователя

```php
$userId = $request->max_user_id;
$user = $request->max_user;
```

### MaxWebhook - Проверка webhook

```php
Route::post('/api/max/webhook', [MaxWebhookController::class, 'handle'])
    ->middleware('max.webhook');
```

### MaxAdmin - Проверка прав администратора

```php
Route::middleware(['max.auth', 'max.admin'])->group(function () {
    Route::post('/api/admin/broadcast', [AdminController::class, 'broadcast']);
});
```

## 📦 Queue Jobs

```php
use App\Jobs\Max\SendMessageJob;
use App\Jobs\Max\SendBroadcastJob;

// Отправить сообщение через очередь
SendMessageJob::dispatch(123456789, 'Текст сообщения');

// Отложенная отправка
SendMessageJob::dispatch(123456789, 'Сообщение')
    ->delay(now()->addMinutes(5));

// Массовая рассылка
SendBroadcastJob::dispatch('Текст для всех');

// Рассылка выбранным пользователям
SendBroadcastJob::dispatch('Текст', [], [1, 2, 3]);
```

## 🎯 Artisan команды

```bash
# Проверка подключения
php artisan max:test

# Установка webhook
php artisan max:set-webhook

# Информация о webhook
php artisan max:webhook-info

# Удаление webhook
php artisan max:delete-webhook
```

## ⚙️ Конфигурация

Все настройки в `config/max.php`:

```php
return [
    'token' => env('MAX_BOT_TOKEN'),
    'secret_key' => env('MAX_SECRET_KEY'),
    'webhook_url' => env('MAX_WEBHOOK_URL'),
    'mini_app_url' => env('MAX_MINI_APP_URL'),
    'bot_username' => env('MAX_BOT_USERNAME'),
    'admin_ids' => [...],
    'notifications' => [...],
    'logging' => [...],
    'validation' => [...],
];
```

## 📖 Дополнительная документация

- [README.md](README.md) - Основная документация
- [LIMITS.md](LIMITS.md) - Лимиты и валидация
- [Официальная документация Max](https://dev.max.ru/docs-api)

