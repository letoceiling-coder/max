# Примеры использования Max API

## Простые примеры

### Отправка сообщений

```php
use App\Max\Max;

// Простое сообщение
Max::send(123456789, 'Привет!');

// С форматированием Markdown
Max::bot()->sendMessage(123456789, '**Жирный** и *курсив*', [
    'format' => 'markdown'
]);

// С форматированием HTML
Max::bot()->sendMessage(123456789, '<b>Жирный</b> и <i>курсив</i>', [
    'format' => 'html'
]);

// С клавиатурой
$keyboard = max_keyboard()
    ->row()
    ->callback('Да', 'yes')
    ->callback('Нет', 'no')
    ->get();

Max::bot()->sendMessage(123456789, 'Согласны?', [
    'attachments' => [$keyboard]
]);
```

### Клавиатуры

```php
// Полная клавиатура
$keyboard = max_keyboard()
    ->row()
    ->callback('🎰 Рулетка', 'wheel')
    ->callback('👥 Друзья', 'friends')
    ->callback('🏆 Топ', 'leaderboard')
    ->row()
    ->link('📱 Сайт', 'https://example.com')
    ->row()
    ->openApp('🚀 Открыть приложение', config('max.mini_app_url'))
    ->row()
    ->requestContact('📞 Отправить контакт')
    ->requestGeoLocation('📍 Моё местоположение')
    ->get();

// Быстрое создание callback клавиатуры
$keyboard = \App\Max\Keyboard::makeCallbacks([
    '🎰 Рулетка' => 'wheel',
    '👥 Друзья' => 'friends',
    '🏆 Топ' => 'leaderboard',
    'ℹ️ Помощь' => 'help',
], columns: 2);
```

## Примеры в контроллерах

### 1. Webhook обработчик

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Max\Max;
use Illuminate\Http\Request;

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
            $keyboard = max_keyboard()
                ->row()
                ->openApp('🎰 Открыть рулетку', config('max.mini_app_url'))
                ->get();
            
            max_send($chatId, '👋 Добро пожаловать в WOW Рулетку!', [
                'attachments' => [$keyboard]
            ]);
        }
    }
    
    protected function handleCallback($callback)
    {
        $callbackId = $callback['id'];
        $payload = $callback['payload'];
        
        if ($payload === 'wheel') {
            Max::bot()->answerCallback($callbackId, [
                'text' => 'Открываем рулетку...'
            ]);
        }
    }
}
```

### 2. Mini App аутентификация

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaxUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('max.auth');
    }
    
    public function getProfile(Request $request)
    {
        $maxUserId = $request->max_user_id;
        $maxUser = $request->max_user;
        
        $user = User::firstOrCreate(
            ['max_user_id' => $maxUserId],
            [
                'name' => $maxUser['name'] ?? 'Max User',
            ]
        );
        
        return response()->json($user);
    }
}
```

### 3. Сервис уведомлений

```php
<?php

namespace App\Services;

use App\Max\Max;
use App\Jobs\Max\SendMessageJob;

class MaxNotificationService
{
    public function notifyNewTicket($user)
    {
        $keyboard = max_keyboard()
            ->row()
            ->openApp('🎰 Крутить рулетку', config('max.mini_app_url'))
            ->get();
        
        SendMessageJob::dispatch(
            $user->max_user_id,
            "🎫 <b>Новый билет!</b>\n\nУ вас восстановился билет для вращения рулетки!",
            [
                'format' => 'html',
                'attachments' => [$keyboard],
            ]
        );
    }
    
    public function notifyWin($user, $amount)
    {
        $keyboard = max_keyboard()
            ->row()
            ->openApp('🎉 Забрать приз', config('max.mini_app_url'))
            ->get();
        
        max_send(
            $user->max_user_id,
            "🎉 **Поздравляем!**\n\nВы выиграли {$amount}₽!",
            [
                'format' => 'markdown',
                'attachments' => [$keyboard],
            ]
        );
    }
}
```

### 4. Массовая рассылка

```php
<?php

namespace App\Console\Commands;

use App\Jobs\Max\SendBroadcastJob;
use Illuminate\Console\Command;

class MaxBroadcastCommand extends Command
{
    protected $signature = 'max:broadcast {message}';
    protected $description = 'Отправить сообщение всем пользователям Max';

    public function handle()
    {
        $message = $this->argument('message');
        
        $keyboard = max_keyboard()
            ->row()
            ->openApp('Открыть приложение', config('max.mini_app_url'))
            ->get();
        
        SendBroadcastJob::dispatch($message, [
            'attachments' => [$keyboard]
        ]);
        
        $this->info('✓ Рассылка запущена!');
    }
}
```

### 5. Работа с чатами

```php
<?php

namespace App\Services;

use App\Max\Max;

class MaxChatService
{
    public function getGroupChats()
    {
        return Max::bot()->getChats();
    }
    
    public function updateGroupChat($chatId, $title, $description)
    {
        return Max::bot()->updateChat($chatId, [
            'title' => $title,
            'description' => $description,
        ]);
    }
    
    public function pinAnnouncement($chatId, $messageId)
    {
        return Max::bot()->pinMessage($chatId, $messageId);
    }
    
    public function promoteModerator($chatId, $userId)
    {
        return Max::bot()->promoteChatAdmin($chatId, $userId);
    }
}
```

## Обработка ошибок

```php
use App\Max\Exceptions\MaxException;
use App\Max\Exceptions\MaxValidationException;

// Обработка ошибок API
try {
    Max::send(123456789, 'Hello');
} catch (MaxException $e) {
    Log::error('Max API error: ' . $e->getMessage());
}

// Обработка ошибок валидации
try {
    $user = max_miniapp()->validateAndGetUser($params);
} catch (MaxValidationException $e) {
    return response()->json(['error' => 'Unauthorized'], 401);
}
```

## Настройка в routes/api.php

```php
use App\Http\Controllers\Api\MaxWebhookController;

// Webhook (защищен middleware)
Route::post('/max/webhook', [MaxWebhookController::class, 'handle'])
    ->middleware('max.webhook');

// API для Mini App (требует аутентификации)
Route::middleware('max.auth')->prefix('max')->group(function () {
    Route::get('/user/profile', [MaxUserController::class, 'getProfile']);
    Route::post('/wheel/spin', [WheelController::class, 'spin']);
});

// Admin API (требует права администратора)
Route::middleware(['max.auth', 'max.admin'])->prefix('max/admin')->group(function () {
    Route::post('/broadcast', [AdminController::class, 'broadcast']);
});
```

## 📖 Дополнительно

- [README.md](README.md) - Основная документация
- [SETUP.md](SETUP.md) - Установка и настройка
- [LIMITS.md](LIMITS.md) - Лимиты и валидация
- [Max Dev Portal](https://dev.max.ru/docs-api)

