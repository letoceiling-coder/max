# Max Bot API для Laravel

Полноценная библиотека для работы с Max Bot API и Mini App.

## Установка

```bash
composer require letoceiling-coder/max
```

## Настройка

### 1. Публикация конфигурации

```bash
php artisan vendor:publish --tag=max-config
```

### 2. Настройка .env

```env
MAX_BOT_TOKEN=your_bot_token_here
MAX_SECRET_KEY=your_secret_key_here
MAX_WEBHOOK_URL=https://your-domain.com/api/max/webhook
MAX_MINI_APP_URL=https://your-domain.com
```

## Использование

### Через фасад

```php
use LetoceilingCoder\Max\Max;

// Отправить сообщение
Max::send(123456789, 'Привет!');

// Создать клавиатуру
$keyboard = Max::keyboard()
    ->callback('Кнопка', 'callback_data')
    ->toArray();

Max::bot()->sendMessage(123456789, 'Выберите действие', [
    'keyboard' => $keyboard
]);
```

### Через сервис контейнер

```php
use LetoceilingCoder\Max\Bot;

$bot = app(Bot::class);
$bot->sendMessage(123456789, 'Привет!');
```

## Документация

Подробная документация находится в `src/README.md` после установки пакета.

## Лицензия

MIT

