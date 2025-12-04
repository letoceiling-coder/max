<?php

namespace LetoceilingCoder\Max;

/**
 * Класс для создания inline клавиатур Max
 * Документация: https://dev.max.ru/docs-api#клавиатура
 * 
 * Лимиты:
 * - Максимум 210 кнопок (30 рядов по 7 кнопок)
 * - Для кнопок link, open_app, request_geo_location, request_contact - до 3 в ряду
 * - Максимальная длина ссылки в кнопке link - 2048 символов
 */
class Keyboard
{
    protected array $buttons = [];

    /**
     * Добавить новый ряд кнопок
     */
    public function row(): self
    {
        $this->buttons[] = [];
        return $this;
    }

    /**
     * Добавить кнопку callback
     * Отправляет событие message_callback через webhook/long polling
     * 
     * @param string $text - Текст на кнопке
     * @param string $payload - Данные callback (передаются в событии)
     */
    public function callback(string $text, string $payload): self
    {
        Validator::validateButtonText($text);
        Validator::validateCallbackPayload($payload);
        
        return $this->addButton([
            'type' => 'callback',
            'text' => $text,
            'payload' => $payload,
        ]);
    }

    /**
     * Добавить кнопку с ссылкой
     * Открывает ссылку в новой вкладке
     * 
     * @param string $text - Текст на кнопке
     * @param string $url - URL (максимум 2048 символов)
     */
    public function link(string $text, string $url): self
    {
        Validator::validateButtonText($text);
        Validator::validateButtonUrl($url);
        
        return $this->addButton([
            'type' => 'link',
            'text' => $text,
            'url' => $url,
        ]);
    }

    /**
     * Добавить кнопку запроса контакта
     * Запрашивает контакт и номер телефона пользователя
     * 
     * @param string $text - Текст на кнопке
     */
    public function requestContact(string $text): self
    {
        return $this->addButton([
            'type' => 'request_contact',
            'text' => $text,
        ]);
    }

    /**
     * Добавить кнопку запроса геолокации
     * Запрашивает местоположение пользователя
     * 
     * @param string $text - Текст на кнопке
     */
    public function requestGeoLocation(string $text): self
    {
        return $this->addButton([
            'type' => 'request_geo_location',
            'text' => $text,
        ]);
    }

    /**
     * Добавить кнопку открытия мини-приложения
     * 
     * @param string $text - Текст на кнопке
     * @param string $appUrl - URL мини-приложения
     */
    public function openApp(string $text, string $appUrl): self
    {
        return $this->addButton([
            'type' => 'open_app',
            'text' => $text,
            'url' => $appUrl,
        ]);
    }

    /**
     * Добавить кнопку отправки сообщения
     * Отправляет боту текстовое сообщение
     * 
     * @param string $text - Текст на кнопке
     * @param string $message - Текст сообщения, которое будет отправлено
     */
    public function message(string $text, string $message): self
    {
        return $this->addButton([
            'type' => 'message',
            'text' => $text,
            'payload' => $message,
        ]);
    }

    /**
     * Добавить кнопку в текущий ряд
     */
    protected function addButton(array $button): self
    {
        if (empty($this->buttons)) {
            $this->row();
        }

        $lastRowIndex = count($this->buttons) - 1;
        $this->buttons[$lastRowIndex][] = $button;

        return $this;
    }

    /**
     * Получить attachment для отправки с сообщением
     */
    public function get(): array
    {
        // Валидация клавиатуры
        Validator::validateKeyboard($this->buttons);
        
        return [
            'type' => 'inline_keyboard',
            'payload' => [
                'buttons' => $this->buttons,
            ],
        ];
    }

    /**
     * Получить JSON для отправки
     */
    public function toJson(): string
    {
        return json_encode($this->get());
    }

    /**
     * Создать клавиатуру из массива
     * 
     * @param array $buttons - Массив кнопок
     * @return array
     */
    public static function make(array $buttons): array
    {
        return [
            'type' => 'inline_keyboard',
            'payload' => [
                'buttons' => $buttons,
            ],
        ];
    }

    /**
     * Быстрое создание клавиатуры с callback кнопками
     * 
     * @param array $buttons - Массив ['text' => 'payload', ...]
     * @param int $columns - Количество кнопок в ряду
     * @return array
     */
    public static function makeCallbacks(array $buttons, int $columns = 3): array
    {
        $keyboard = new self();
        $count = 0;

        foreach ($buttons as $text => $payload) {
            if ($count % $columns === 0) {
                $keyboard->row();
            }
            $keyboard->callback($text, $payload);
            $count++;
        }

        return $keyboard->get();
    }
}

