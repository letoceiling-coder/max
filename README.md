# Max Bot API для Laravel

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.2-blue.svg)](https://www.php.net/)

Полноценная библиотека для работы с Max Bot API и Mini App в Laravel приложениях.

## ✨ Возможности

- 🤖 **30+ методов Bot API** - отправка сообщений, управление чатами, участниками
- 📱 **Mini App интеграция** - валидация параметров, работа с пользователями
- ⌨️ **Конструктор клавиатур** - inline клавиатуры с callback, ссылками, Mini App
- ✅ **Автоматическая валидация** - проверка всех данных перед отправкой
- 📊 **Типы данных** - удобные классы для User, Chat, Message
- 🔄 **Webhook поддержка** - подписка на обновления
- 📤 **Загрузка файлов** - удобная загрузка медиа

## 📦 Установка

### Через Composer

```bash
composer require letoceiling-coder/max
```

### Публикация конфигурации

```bash
php artisan vendor:publish --tag=max-config
```

### Настройка .env

```env
MAX_BOT_TOKEN=your_bot_token_here
MAX_SECRET_KEY=your_secret_key_here
MAX_WEBHOOK_URL=https://your-domain.com/api/max/webhook
MAX_MINI_APP_URL=https://your-domain.com
MAX_BOT_USERNAME=your_bot_username
MAX_ADMIN_IDS=123456789,987654321
```

## 🚀 Быстрый старт

### Отправка сообщения

```php
use LetoceilingCoder\Max\Max;

// Простое сообщение
Max::send(123456789, 'Привет! 👋');

// С форматированием
Max::bot()->sendMessage(123456789, '**Жирный** текст', [
    'format' => 'markdown'
]);
```

### Создание клавиатуры

```php
use LetoceilingCoder\Max\Max;

$keyboard = Max::keyboard()
    ->row()
    ->callback('Кнопка 1', 'btn1')
    ->callback('Кнопка 2', 'btn2')
    ->row()
    ->link('Открыть сайт', 'https://example.com')
    ->openApp('Открыть Mini App', 'https://app.example.com')
    ->get();

Max::bot()->sendMessage(123456789, 'Выберите действие:', [
    'attachments' => [$keyboard]
]);
```

### Валидация Mini App

```php
use LetoceilingCoder\Max\MiniApp;

$miniApp = new MiniApp();
$params = $request->header('X-Max-Init-Data');

if ($miniApp->validateParams($params)) {
    $user = $miniApp->getUser($params);
    $userId = $user['user_id'];
}
```

## 📚 Основные возможности

### Отправка сообщений

```php
use LetoceilingCoder\Max\Max;

// Текстовое сообщение
Max::bot()->sendMessage(123456789, 'Текст сообщения');

// С форматированием Markdown
Max::bot()->sendMessage(123456789, '**Жирный** *курсив*', [
    'format' => 'markdown'
]);

// С форматированием HTML
Max::bot()->sendMessage(123456789, '<b>Жирный</b> <i>курсив</i>', [
    'format' => 'html'
]);
```

### Работа с клавиатурами

```php
use LetoceilingCoder\Max\Keyboard;

// Inline клавиатура
$keyboard = new Keyboard();

// Callback кнопки
$keyboard->row()
    ->callback('🎰 Рулетка', 'wheel')
    ->callback('👥 Друзья', 'friends');

// Ссылка
$keyboard->row()
    ->link('📱 Сайт', 'https://example.com');

// Открыть Mini App
$keyboard->row()
    ->openApp('🚀 Открыть приложение', 'https://app.example.com');

// Запросить контакт
$keyboard->row()
    ->requestContact('📞 Отправить контакт');

// Запросить геолокацию
$keyboard->row()
    ->requestGeoLocation('📍 Отправить геолокацию');

$attachment = $keyboard->get();

Max::bot()->sendMessage($chatId, 'Выберите действие:', [
    'attachments' => [$attachment]
]);
```

### Работа с чатами

```php
use LetoceilingCoder\Max\Max;

// Получить список чатов
$chats = Max::bot()->getChats();

// Получить информацию о чате
$chat = Max::bot()->getChat($chatId);

// Изменить информацию о чате
Max::bot()->updateChat($chatId, [
    'title' => 'Новое название',
    'description' => 'Новое описание'
]);

// Получить участников
$members = Max::bot()->getChatMembers($chatId);

// Добавить участников
Max::bot()->addChatMembers($chatId, [123, 456, 789]);

// Назначить администратора
Max::bot()->promoteChatAdmin($chatId, 123);
```

### Webhook

```php
use LetoceilingCoder\Max\Max;

// Установить webhook
Max::bot()->subscribe('https://yourdomain.com/api/max/webhook');

// Получить статус webhook
$subscriptions = Max::bot()->getSubscriptions();

// Удалить webhook
Max::bot()->unsubscribe();
```

### Загрузка файлов

```php
use LetoceilingCoder\Max\Max;

// Загрузить файл
$result = Max::bot()->upload([
    'file' => [
        'path' => '/path/to/file.jpg',
        'filename' => 'photo.jpg'
    ]
]);
```

## 🎯 Использование в контроллерах

### Webhook обработчик

```php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use LetoceilingCoder\Max\Max;

class MaxWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $update = $request->all();
        
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }
        
        if (isset($update['callback'])) {
            $this->handleCallback($update['callback']);
        }
        
        return response()->json(['ok' => true]);
    }
    
    protected function handleMessage($message)
    {
        $chatId = $message['chat_id'];
        $text = $message['text'] ?? '';
        
        if ($text === '/start') {
            Max::send($chatId, 'Добро пожаловать!');
        }
    }
    
    protected function handleCallback($callback)
    {
        $callbackId = $callback['id'];
        $payload = $callback['payload'];
        
        Max::bot()->answerCallback($callbackId, [
            'text' => 'Обработано!'
        ]);
    }
}
```

### Middleware для Mini App

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use LetoceilingCoder\Max\MiniApp;
use LetoceilingCoder\Max\Exceptions\MaxValidationException;

class MaxAuth
{
    public function handle(Request $request, Closure $next)
    {
        $params = $request->header('X-Max-Init-Data');
        
        if (!$params) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $miniApp = new MiniApp();
            $user = $miniApp->validateAndGetUser($params);
            $request->merge(['max_user' => $user]);
        } catch (MaxValidationException $e) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }
        
        return $next($request);
    }
}
```

## 📖 API Документация

### Основные классы

- **`Max`** - Фасад для быстрого доступа
- **`Bot`** - Работа с Bot API (30+ методов)
- **`MiniApp`** - Валидация Mini App
- **`Keyboard`** - Конструктор клавиатур

### Основные методы Bot API

#### Bots
- `getBotInfo()` - Получить информацию о боте

#### Messages
- `sendMessage()` - Отправить сообщение
- `editMessage()` - Редактировать сообщение
- `deleteMessage()` - Удалить сообщение
- `getMessage()` - Получить сообщение
- `getMessages()` - Получить список сообщений

#### Chats
- `getChats()` - Получить список чатов
- `getChat()` - Получить информацию о чате
- `updateChat()` - Изменить чат
- `getChatMembers()` - Получить участников
- `addChatMembers()` - Добавить участников
- `removeChatMember()` - Удалить участника
- `promoteChatAdmin()` - Назначить администратора
- `demoteChatAdmin()` - Снять права администратора

#### Subscriptions
- `subscribe()` - Подписаться (установить webhook)
- `unsubscribe()` - Отписаться (удалить webhook)
- `getSubscriptions()` - Получить подписки
- `getUpdates()` - Получить обновления

Полный список методов смотрите в [src/README.md](src/README.md)

## 🎨 Форматирование текста

### Markdown

```php
Max::bot()->sendMessage($chatId, '**Жирный** *курсив* ~~зачеркнутый~~', [
    'format' => 'markdown'
]);
```

### HTML

```php
Max::bot()->sendMessage($chatId, '<b>Жирный</b> <i>курсив</i> <del>зачеркнутый</del>', [
    'format' => 'html'
]);
```

## 🚨 Обработка ошибок

```php
use LetoceilingCoder\Max\Exceptions\MaxException;

try {
    Max::send(123456789, 'Hello');
} catch (MaxException $e) {
    Log::error('Max API error: ' . $e->getMessage());
}
```

## 🛠️ Дополнительная документация

- **[src/README.md](src/README.md)** - Полная документация с примерами
- **[src/EXAMPLES.md](src/EXAMPLES.md)** - Примеры использования
- **[src/LIMITS.md](src/LIMITS.md)** - Лимиты и валидация
- **[src/FEATURES.md](src/FEATURES.md)** - Полный список возможностей
- **[src/SETUP.md](src/SETUP.md)** - Подробная инструкция по установке

## 🔗 Официальная документация Max

- [Max API](https://dev.max.ru/docs-api)
- [Max Mini Apps](https://dev.max.ru/docs-miniapps)

## 📝 Лицензия

MIT License. См. [LICENSE](LICENSE) файл для деталей.

## 🤝 Поддержка

Если у вас есть вопросы или предложения, создайте [Issue](https://github.com/letoceiling-coder/max/issues) в репозитории.
