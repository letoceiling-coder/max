# Max API для Laravel

Полная интеграция с Max Bot API (https://dev.max.ru/docs-api)

## 📁 Структура

```
app/Max/
├── MaxClient.php           # Базовый клиент для HTTP-запросов
├── Bot.php                 # Все методы Max Bot API (30+ методов)
├── MiniApp.php             # Валидация Max Mini Apps
├── Keyboard.php            # Создание inline клавиатур
├── Max.php                 # Фасад для удобного доступа
├── Exceptions/             # Исключения
│   ├── MaxException.php
│   └── MaxValidationException.php
└── Types/                  # Типы данных
    ├── User.php
    ├── Chat.php
    └── Message.php
```

## 🚀 Установка

### 1. Настройка config/services.php

```php
'max' => [
    'token' => env('MAX_BOT_TOKEN'),
    'secret_key' => env('MAX_SECRET_KEY'),
],
```

### 2. Настройка .env

```env
MAX_BOT_TOKEN=your_bot_token_here
MAX_SECRET_KEY=your_secret_key_here
```

## 📚 Использование

### Быстрый старт

```php
use App\Max\Max;

// Отправить сообщение
Max::send(123456789, 'Привет!');

// Валидировать Mini App
$isValid = Max::validateMiniApp($params);
$user = Max::getMiniAppUser($params);

// Создать клавиатуру
$keyboard = Max::keyboard()
    ->row()
    ->callback('Кнопка 1', 'btn1')
    ->callback('Кнопка 2', 'btn2')
    ->get();
```

### Отправка сообщений

```php
use App\Max\Max;

// Простое текстовое сообщение
Max::bot()->sendMessage(123456789, 'Текст сообщения');

// С форматированием (Markdown)
Max::bot()->sendMessage(123456789, '**Жирный** и *курсив*', [
    'format' => 'markdown'
]);

// С форматированием (HTML)
Max::bot()->sendMessage(123456789, '<b>Жирный</b> и <i>курсив</i>', [
    'format' => 'html'
]);

// С клавиатурой
$keyboard = Max::keyboard()
    ->row()
    ->callback('Да', 'yes')
    ->callback('Нет', 'no')
    ->get();

Max::bot()->sendMessage(123456789, 'Выберите:', [
    'attachments' => [$keyboard]
]);
```

### Работа с клавиатурами

```php
use App\Max\Keyboard;

// Inline клавиатура с разными типами кнопок
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

// Отправка с клавиатурой
Max::bot()->sendMessage($chatId, 'Выберите действие:', [
    'attachments' => [$attachment]
]);

// Быстрое создание callback клавиатуры
$keyboard = Keyboard::makeCallbacks([
    'Кнопка 1' => 'callback_1',
    'Кнопка 2' => 'callback_2',
    'Кнопка 3' => 'callback_3',
], columns: 2); // 2 кнопки в ряду
```

### Редактирование и удаление сообщений

```php
// Редактировать сообщение
Max::bot()->editMessage($messageId, 'Новый текст');

// Удалить сообщение
Max::bot()->deleteMessage($messageId);

// Получить сообщение
$message = Max::bot()->getMessage($messageId);
```

### Работа с чатами

```php
// Получить список чатов
$chats = Max::bot()->getChats();

// Получить информацию о чате
$chat = Max::bot()->getChat($chatId);

// Изменить информацию о чате
Max::bot()->updateChat($chatId, [
    'title' => 'Новое название',
    'description' => 'Новое описание'
]);

// Отправить действие (typing)
Max::bot()->sendChatAction($chatId, 'typing');

// Закрепить сообщение
Max::bot()->pinMessage($chatId, $messageId);

// Открепить сообщение
Max::bot()->unpinMessage($chatId);

// Получить участников чата
$members = Max::bot()->getChatMembers($chatId);

// Добавить участников
Max::bot()->addChatMembers($chatId, [123, 456, 789]);

// Удалить участника
Max::bot()->removeChatMember($chatId, 123);

// Назначить администратора
Max::bot()->promoteChatAdmin($chatId, 123);

// Снять права администратора
Max::bot()->demoteChatAdmin($chatId, 123);

// Покинуть чат
Max::bot()->leaveChat($chatId);
```

### Webhook и обновления

```php
// Установить webhook
Max::bot()->subscribe('https://yourdomain.com/api/max/webhook');

// Получить статус webhook
$subscriptions = Max::bot()->getSubscriptions();

// Удалить webhook
Max::bot()->unsubscribe();

// Long polling (получить обновления)
$updates = Max::bot()->getUpdates();
```

### Загрузка файлов

```php
// Загрузить файл
$result = Max::bot()->upload([
    'file' => [
        'path' => '/path/to/file.jpg',
        'filename' => 'photo.jpg'
    ]
]);

// Или с содержимым файла
$result = Max::bot()->upload([
    'file' => [
        'content' => file_get_contents('/path/to/file.jpg'),
        'filename' => 'photo.jpg'
    ]
]);
```

### Mini App валидация

```php
use App\Max\MiniApp;

$miniApp = new MiniApp();

// Валидировать параметры
if ($miniApp->validateParams($params)) {
    $user = $miniApp->getUser($params);
    $userId = $user['user_id'];
}

// Или с исключением
try {
    $user = $miniApp->validateAndGetUser($params);
} catch (\App\Max\Exceptions\MaxValidationException $e) {
    return response()->json(['error' => 'Unauthorized'], 401);
}

// Создать URL для Mini App
$url = $miniApp->createAppUrl('app_id', ['param' => 'value']);
```

### Использование типов данных

```php
use App\Max\Types\User;
use App\Max\Types\Chat;
use App\Max\Types\Message;

// Преобразовать из массива
$user = User::fromArray($userData);
echo $user->name;
echo $user->userId;

// Обратно в массив
$array = $user->toArray();

// Работа с Chat
$chat = Chat::fromArray($chatData);
if ($chat->isDialog()) {
    // Диалог
} elseif ($chat->isGroup()) {
    // Группа
}
```

## 🎯 Все методы Bot API

### Bots
- `getBotInfo()` - Получить информацию о боте

### Messages
- `getMessages($params)` - Получить список сообщений
- `sendMessage($chatId, $text, $params)` - Отправить сообщение
- `editMessage($messageId, $text, $params)` - Редактировать сообщение
- `deleteMessage($messageId)` - Удалить сообщение
- `getMessage($messageId)` - Получить сообщение
- `getVideoInfo($videoUrl)` - Получить информацию о видео
- `answerCallback($callbackId, $params)` - Ответить на callback

### Chats
- `getChats($params)` - Получить список чатов
- `getChatByLink($link)` - Получить чат по ссылке
- `getChat($chatId)` - Получить информацию о чате
- `updateChat($chatId, $data)` - Изменить чат
- `deleteChat($chatId)` - Удалить чат
- `sendChatAction($chatId, $action)` - Отправить действие
- `getPinnedMessage($chatId)` - Получить закрепленное сообщение
- `pinMessage($chatId, $messageId)` - Закрепить сообщение
- `unpinMessage($chatId)` - Открепить сообщение
- `getBotMembership($chatId)` - Информация о членстве бота
- `leaveChat($chatId)` - Покинуть чат
- `getChatAdmins($chatId)` - Получить администраторов
- `promoteChatAdmin($chatId, $userId)` - Назначить администратора
- `demoteChatAdmin($chatId, $userId)` - Снять права администратора
- `getChatMembers($chatId, $params)` - Получить участников
- `addChatMembers($chatId, $userIds)` - Добавить участников
- `removeChatMember($chatId, $userId)` - Удалить участника

### Subscriptions
- `getSubscriptions()` - Получить подписки
- `subscribe($url, $params)` - Подписаться (установить webhook)
- `unsubscribe()` - Отписаться (удалить webhook)
- `getUpdates($params)` - Получить обновления (long polling)

### Upload
- `upload($files)` - Загрузить файлы

## 🔒 Middleware (TODO)

Создайте middleware для валидации webhook запросов:

```php
namespace App\Http\Middleware;

use App\Max\MiniApp;
use Closure;

class MaxAuth
{
    public function handle($request, Closure $next)
    {
        $params = $request->header('X-Max-Init-Data');
        
        if (!$params || !app(MiniApp::class)->validateParams($params)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return $next($request);
    }
}
```

## 💡 Примеры в контроллерах

### Webhook обработчик

```php
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

### Сервис уведомлений

```php
class MaxNotificationService
{
    public function notifyNewTicket($user)
    {
        $keyboard = Max::keyboard()
            ->row()
            ->openApp('🎰 Крутить рулетку', config('app.max_app_url'))
            ->get();
        
        Max::bot()->sendMessage(
            $user->max_user_id,
            "🎫 У вас новый билет!",
            ['attachments' => [$keyboard]]
        );
    }
}
```

## 📖 Официальная документация

- Max API: https://dev.max.ru/docs-api
- Max Mini Apps: https://dev.max.ru/docs-miniapps
- Swagger: https://dev.max.ru/docs-api (скачать swagger.json)

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
use App\Max\Exceptions\MaxException;

try {
    Max::send(123456789, 'Hello');
} catch (MaxException $e) {
    Log::error('Max API error: ' . $e->getMessage());
}
```

## ⚙️ HTTP коды ответов

- `200` — успешная операция
- `400` — недействительный запрос
- `401` — ошибка аутентификации
- `404` — ресурс не найден
- `405` — метод не допускается
- `429` — превышено количество запросов
- `503` — сервис недоступен

